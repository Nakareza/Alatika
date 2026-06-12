<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index(Request $request)
{
    $query = Peminjaman::with(['user', 'alat']);

    // Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Search
    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->whereHas('user', function ($user) use ($search) {
                $user->where('name', 'like', "%{$search}%");
            })

            ->orWhereHas('alat', function ($alat) use ($search) {
                $alat->where('nama', 'like', "%{$search}%");
            })

            ->orWhere('kode_peminjaman', 'like', "%{$search}%");
        });
    }

    $peminjaman = $query
        ->orderBy('created_at', 'desc')
        ->get();

    $stats = [
        'total' => Peminjaman::count(),
        'pending' => Peminjaman::where('status', 'pending')->count(),
        'aktif' => Peminjaman::where('status', 'dipinjam')->count(),
        'ditolak' => Peminjaman::where('status', 'ditolak')->count(),
    ];

    return view(
        'admin.peminjaman.index',
        compact('peminjaman', 'stats')
    );
}

    public function approve($id, TelegramService $telegram)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        // Check apakah kalab sudah approve terlebih dahulu
        if (!$peminjaman->kalab_approved_at) {
            return redirect()->back()->with('error', 'Peminjaman belum disetujui oleh Kalab. Mohon tunggu persetujuan Kalab terlebih dahulu.');
        }

        // Admin approve: set admin_approved_by/at
        $peminjaman->update([
            'admin_approved_by' => Auth::id(),
            'admin_approved_at' => now(),
        ]);

        // Jika kalab dan admin sudah approve, ubah status ke dipinjam
        if ($peminjaman->kalab_approved_at && $peminjaman->admin_approved_at) {
            $peminjaman->update(['status' => 'dipinjam']);
        }

        // Send notif
        $telegram->notifyPeminjamanApproved($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'jumlah' => $peminjaman->jumlah,
            'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
            'approver_role' => 'Admin',
        ]);

        return redirect()->back()->with('success', 'Peminjaman Dosen disetujui oleh Admin. Persetujuan lengkap!');
    }

    public function reject(Request $request, $id, TelegramService $telegram)
    {
        $request->validate(['alasan' => 'required|string']);

        $peminjaman = Peminjaman::findOrFail($id);
        
        // Admin reject
        $peminjaman->update([
            'status' => 'ditolak',
            'rejected_reason' => $request->alasan,
            'admin_approved_by' => Auth::id(),
            'admin_approved_at' => now(),
        ]);
        
        // Send notif
        $telegram->notifyPeminjamanRejected($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'alasan' => $request->alasan,
        ]);

        return redirect()->back()->with('success', 'Peminjaman ditolak oleh Admin.');
    }

    public function markAsBorrowed($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status !== 'dipinjam') {
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

    public function approveReturn($id)
{
    $peminjaman = Peminjaman::findOrFail($id);

    if ($peminjaman->status !== 'menunggu_verifikasi') {

        return back()->with(
            'error',
            'Status tidak valid'
        );
    }

    // update status
    $peminjaman->update([
        'status' => 'selesai',
        'approved_by' => Auth::id(),
        'approved_at' => now(),
    ]);

    // kembalikan stok
    if ($peminjaman->alat) {

        $peminjaman->alat->increment(
            'stok_tersedia',
            $peminjaman->jumlah
        );
    }

    return back()->with(
        'success',
        'Pengembalian berhasil diverifikasi'
    );
}

public function rejectReturn($id)
{
    $peminjaman = Peminjaman::findOrFail($id);

    $peminjaman->update([
        'status' => 'dipinjam',
    ]);

    return redirect()
        ->back()
        ->with('success', 'Pengembalian ditolak.');
}

}
