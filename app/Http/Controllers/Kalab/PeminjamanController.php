<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function persetujuan(Request $request)
    {
        $query = Peminjaman::with(['user', 'alat'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'dosen');
            });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('alat', function ($a) use ($search) {
                    $a->where('nama', 'like', "%{$search}%");
                });
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $peminjaman = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistik
        $stats = [

            // Menunggu persetujuan Kalab
            'pending' => Peminjaman::whereHas('user', function ($q) {
                $q->where('role', 'dosen');
            })
            ->where('status', 'pending')
            ->count(),

            // Sedang dipinjam
            'disetujui_bulan_ini' => Peminjaman::whereHas('user', function ($q) {
                $q->where('role', 'dosen');
            })
            ->where('status', 'disetujui')
            ->count(),

            // Sudah dikembalikan
            'dikembalikan_bulan_ini' => Peminjaman::whereHas('user', function ($q) {
                $q->where('role', 'dosen');
            })
            ->where('status', 'dikembalikan')
            ->count(),

            // Total seluruh pengajuan
            'total_pengajuan' => Peminjaman::whereHas('user', function ($q) {
                $q->where('role', 'dosen');
            })
            ->count(),
        ];

        return view(
            'kalab.persetujuan.index',
            compact('peminjaman', 'stats')
        );
    }

    public function riwayat()
    {
        // Ka lab sees all history
        $peminjaman = Peminjaman::with(['user', 'alat'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('kalab.riwayat.index', compact('peminjaman'));
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'alat',
            'approvedBy'
        ])->findOrFail($id);

        return view(
            'kalab.peminjaman.show',
            compact('peminjaman')
        );
    }

    public function approve($id, TelegramService $telegram)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        // Kalab approve: set kalab_approved_by/at, jangan ubah status
        $peminjaman->update([
            'kalab_approved_by' => Auth::id(),
            'kalab_approved_at' => now(),
        ]);

        $telegram->notifyPeminjamanApproved($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'jumlah' => $peminjaman->jumlah,
            'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
            'approver_role' => 'Kepala Lab',
        ]);

        return redirect()->back()->with('success', 'Peminjaman Dosen disetujui oleh Kalab. Menunggu persetujuan Admin.');
    }

    public function reject(Request $request, $id, TelegramService $telegram)
    {
        $request->validate(['alasan' => 'required|string']);

        $peminjaman = Peminjaman::findOrFail($id);
        
        $peminjaman->update([
            'status' => 'ditolak',
            'rejected_reason' => $request->alasan,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        $telegram->notifyPeminjamanRejected($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'alasan' => $request->alasan,
        ]);

        return redirect()->back()->with('success', 'Peminjaman Dosen ditolak');
    }

    public function bulkApprove(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'peminjaman_ids' => 'required|array',
            'peminjaman_ids.*' => 'exists:peminjaman,id',
        ]);

        $ids = $request->peminjaman_ids;
        $peminjamans = Peminjaman::whereIn('id', $ids)->where('status', 'pending')->get();

        $approvedCount = 0;

        foreach ($peminjamans as $peminjaman) {
            $peminjaman->update([
                'kalab_approved_by' => Auth::id(),
                'kalab_approved_at' => now(),
            ]);

            $telegram->notifyPeminjamanApproved($peminjaman->user, [
                'kode' => $peminjaman->kode_peminjaman,
                'alat' => $peminjaman->alat->nama,
                'jumlah' => $peminjaman->jumlah,
                'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                'approver_role' => 'Kepala Lab',
            ]);

            $approvedCount++;
        }

        return redirect()->back()->with('success', "$approvedCount Peminjaman Dosen berhasil disetujui oleh Kalab. Menunggu persetujuan Admin!");
    }
}
