<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\ToolSet;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        // Admin hanya menangani pengembalian MAHASISWA
        $query = Peminjaman::with(['user', 'borrowable'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'mahasiswa');
            })
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
                ->orWhere(function ($sub) use ($search) {
                    $sub->where(function ($q1) use ($search) {
                        $q1->where('borrowable_type', Alat::class)
                           ->whereHasMorph('borrowable', [Alat::class], function ($q) use ($search) {
                               $q->where('nama', 'like', "%{$search}%");
                           });
                    })->orWhere(function ($q2) use ($search) {
                        $q2->where('borrowable_type', ToolSet::class)
                           ->whereHasMorph('borrowable', [ToolSet::class], function ($q) use ($search) {
                               $q->where('nama_tool_set', 'like', "%{$search}%");
                           });
                    });
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

        $mhs = Peminjaman::whereHas('user', fn($q) => $q->where('role', 'mahasiswa'));
        $stats = [
            'total'      => (clone $mhs)->count(),
            'selesai'    => (clone $mhs)->where('status', 'selesai')->count(),
            'dipinjam'   => (clone $mhs)->where('status', 'dipinjam')->count(),
            'verifikasi' => (clone $mhs)->where('status', 'menunggu_verifikasi')->count(),
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

        $peminjaman = Peminjaman::with('borrowable')->findOrFail($id);
        
        $borrowable = $peminjaman->borrowable;
        if ($borrowable) {
            if ($request->kondisi_kembali === 'baik' || $request->kondisi_kembali === 'rusak_ringan') {
                $borrowable->stok_tersedia += $peminjaman->jumlah;
            } else {
                // Rusak berat
                if ($peminjaman->borrowable_type === ToolSet::class) {
                    $borrowable->stok -= $peminjaman->jumlah;
                } else {
                    $borrowable->stok_total -= $peminjaman->jumlah;
                }
            }
            $borrowable->save();
        }

        $peminjaman->update([
            'status' => 'selesai',
            'kondisi_kembali' => $request->kondisi_kembali,
            'catatan_kondisi' => $request->catatan_kondisi,
        ]);

        $itemName = $peminjaman->borrowable_type === ToolSet::class ? $borrowable->nama_tool_set : $borrowable->nama;

        // Send notif
        $telegram->notifyReturnVerified($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $itemName,
        ]);

        // Process waitlist (only for Alat unit)
        if ($peminjaman->borrowable_type === Alat::class && $borrowable && $borrowable->stok_tersedia > 0) {
            $waitlists = \App\Models\Waitlist::where('alat_id', $borrowable->id)
                ->where('status', 'waiting')
                ->get();

            foreach ($waitlists as $waiter) {
                if ($waiter->user) {
                    $telegram->notifyWaitlistRestock($waiter->user, [
                        'alat' => $borrowable->nama
                    ]);
                }
                $waiter->update(['status' => 'notified']);
            }
        }

        return redirect()->back()->with('success', 'Pengembalian berhasil diverifikasi');
    }
}
