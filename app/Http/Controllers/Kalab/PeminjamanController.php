<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function persetujuan()
    {
        // Ka Lab sees ALL pending requests from Dosen
        $peminjaman = Peminjaman::with(['user', 'alat'])
            ->whereHas('user', function($q) {
                $q->where('role', 'dosen');
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('kalab.persetujuan.index', compact('peminjaman'));
    }

    public function riwayat()
    {
        // Ka lab sees all history
        $peminjaman = Peminjaman::with(['user', 'alat'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('kalab.riwayat.index', compact('peminjaman'));
    }

    public function approve($id, TelegramService $telegram)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        $peminjaman->update([
            'status' => 'disetujui',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $telegram->notifyPeminjamanApproved($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'jumlah' => $peminjaman->jumlah,
            'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
            'approver_role' => 'Kepala Lab',
        ]);

        return redirect()->back()->with('success', 'Peminjaman Dosen disetujui');
    }

    public function reject(Request $request, $id, TelegramService $telegram)
    {
        $request->validate(['alasan' => 'required|string']);

        $peminjaman = Peminjaman::findOrFail($id);
        
        $peminjaman->update([
            'status' => 'ditolak',
            'rejected_reason' => $request->alasan,
            'approved_by' => auth()->id(),
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
                'status' => 'disetujui',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
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

        return redirect()->back()->with('success', "$approvedCount Peminjaman Dosen berhasil disetujui secara massal!");
    }
}
