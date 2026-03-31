<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index()
    {
        // Load real data from db
        $peminjaman = Peminjaman::with(['user', 'alat'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.peminjaman.index', compact('peminjaman'));
    }

    public function approve($id, TelegramService $telegram)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        $peminjaman->update([
            'status' => 'disetujui',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Send notif
        $telegram->notifyPeminjamanApproved($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'jumlah' => $peminjaman->jumlah,
            'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
            'approver_role' => auth()->user()->role === 'kalab' ? 'Kepala Lab' : 'Admin',
        ]);

        return redirect()->back()->with('success', 'Peminjaman disetujui');
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

        // Restore stok (if logic was deducting early, usually done on approve, but we'll assume it's just rejected)
        
        // Send notif
        $telegram->notifyPeminjamanRejected($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'alasan' => $request->alasan,
        ]);

        return redirect()->back()->with('success', 'Peminjaman ditolak');
    }

    public function markAsBorrowed($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status !== 'disetujui') {
            return redirect()->back()->with('error', 'Peminjaman harus disetujui dahulu.');
        }

        // Check stock
        if (!$peminjaman->alat->isAvailable($peminjaman->jumlah)) {
            return redirect()->back()->with('error', 'Stok alat tidak mencukupi saat ini.');
        }

        // Deduct stock
        $alat = $peminjaman->alat;
        $alat->stok_tersedia -= $peminjaman->jumlah;
        $alat->save();

        $peminjaman->update([
            'status' => 'dipinjam',
        ]);

        return redirect()->back()->with('success', 'Status diubah menjadi DIPINJAM. Stok alat dikurangi.');
    }
}
