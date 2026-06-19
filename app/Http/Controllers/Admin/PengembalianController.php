<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $query = Peminjaman::with(['user', 'alat'])
            ->whereIn('status', [
                'dipinjam',
                'menunggu_verifikasi',
                'selesai'
            ]);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search mahasiswa / alat / kode
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->whereHas('user', function ($user) use ($search) {
                    $user->where('name', 'like', "%{$search}%")
                        ->orWhere('nim', 'like', "%{$search}%");
                })

                ->orWhereHas('alat', function ($alat) use ($search) {
                    $alat->where('nama', 'like', "%{$search}%");
                })

                ->orWhere('kode_peminjaman', 'like', "%{$search}%");

            });
        }

        $pengembalian = $query
            ->orderByRaw("
                FIELD(
                    status,
                    'menunggu_verifikasi',
                    'dipinjam',
                    'selesai'
                )
            ")
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total'      => Peminjaman::count(),
            'selesai'    => Peminjaman::where('status', 'selesai')->count(),
            'dipinjam'   => Peminjaman::where('status', 'dipinjam')->count(),
            'verifikasi' => Peminjaman::where('status', 'menunggu_verifikasi')->count(),
        ];

        return view(
            'admin.pengembalian.index',
            compact('pengembalian', 'stats')
        );
    }

    public function verify(Request $request, $id, TelegramService $telegram)
    {
        $request->validate([
            'kondisi_kembali' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan_kondisi' => 'nullable|string'
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        
        // Return stok
        $alat = $peminjaman->alat;
        if ($request->kondisi_kembali === 'baik' || $request->kondisi_kembali === 'rusak_ringan') {
            $alat->stok_tersedia += $peminjaman->jumlah;
        } else {
            // Jika rusak berat, stok tidak dikembalikan ke stok tersedia
            // (tergantung business logic lab, biasanya dikurangi dari stok total juga / masuk maintenance)
            $alat->stok_total -= $peminjaman->jumlah;
        }
        $alat->save();

        $peminjaman->update([
            'status' => 'selesai',
            'kondisi_kembali' => $request->kondisi_kembali,
            'catatan_kondisi' => $request->catatan_kondisi,
            // Jika belum ada foto karena offline return, kita biarkan null atau bisa upload admin manual
        ]);

        // Send notif
        $telegram->notifyReturnVerified($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
        ]);

        // Process waitlist
        if ($alat->stok_tersedia > 0) {
            $waitlists = \App\Models\Waitlist::where('alat_id', $alat->id)
                ->where('status', 'waiting')
                ->get();

            foreach ($waitlists as $waiter) {
                // Send telegram notification to each waitlisted user
                if ($waiter->user) {
                    $telegram->notifyWaitlistRestock($waiter->user, [
                        'alat' => $alat->nama
                    ]);
                }
                
                // Update status to notified so they don't get spammed next return
                $waiter->update(['status' => 'notified']);
            }
        }

        return redirect()->back()->with('success', 'Pengembalian berhasil diverifikasi');
    }
}
