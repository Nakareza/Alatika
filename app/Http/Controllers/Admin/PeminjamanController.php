<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        // Admin hanya menangani peminjaman MAHASISWA
        $query = Peminjaman::with(['user', 'alat'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'mahasiswa');
            });

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

        $peminjaman = $query->orderBy('created_at', 'desc')->get();

        // Stats hanya untuk mahasiswa
        $mhs = Peminjaman::whereHas('user', fn($q) => $q->where('role', 'mahasiswa'));
        $stats = [
            'total'   => (clone $mhs)->count(),
            'pending' => (clone $mhs)->where('status', 'pending')->count(),
            'aktif'   => (clone $mhs)->where('status', 'dipinjam')->count(),
            'ditolak' => (clone $mhs)->where('status', 'ditolak')->count(),
        ];

        $keperluanOptions = $this->getKeperluanOptions();

        return view('admin.peminjaman.index', compact('peminjaman', 'stats', 'keperluanOptions'));
    }

    public function approve($id, TelegramService $telegram)
    {
        $peminjaman = Peminjaman::with(['user', 'alat'])->findOrFail($id);

        // Guard: admin hanya approve mahasiswa
        if ($peminjaman->user->role !== 'mahasiswa') {
            return redirect()->back()->with('error', 'Admin hanya dapat menyetujui peminjaman mahasiswa.');
        }

        // Check stock availability before approving
        $alat = $peminjaman->alat;
        if ($alat->stok_tersedia < $peminjaman->jumlah) {
            return redirect()->back()->with('error', 'Stok alat "' . $alat->nama . '" tidak mencukupi (tersedia: ' . $alat->stok_tersedia . ', diminta: ' . $peminjaman->jumlah . ').');
        }

        // Decrement stock upon approval
        $alat->decrement('stok_tersedia', $peminjaman->jumlah);

        // Admin langsung setujui (sole approver untuk mahasiswa)
        $peminjaman->update([
            'admin_approved_by' => Auth::id(),
            'admin_approved_at' => now(),
            'status' => 'dipinjam',
        ]);

        $telegram->notifyPeminjamanApproved($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'jumlah' => $peminjaman->jumlah,
            'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
            'approver_role' => 'Admin',
        ]);

        return redirect()->back()->with('success', 'Peminjaman Mahasiswa disetujui. Status: Dipinjam.');
    }

    public function reject(Request $request, $id, TelegramService $telegram)
    {
        $request->validate(['alasan' => 'required|string']);

        $peminjaman = Peminjaman::with('user')->findOrFail($id);

        // Guard: admin hanya reject mahasiswa
        if ($peminjaman->user->role !== 'mahasiswa') {
            return redirect()->back()->with('error', 'Admin hanya dapat menolak peminjaman mahasiswa.');
        }

        $peminjaman->update([
            'status' => 'ditolak',
            'rejected_reason' => $request->alasan,
            'admin_approved_by' => Auth::id(),
            'admin_approved_at' => now(),
        ]);

        $telegram->notifyPeminjamanRejected($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'alasan' => $request->alasan,
        ]);

        return redirect()->back()->with('success', 'Peminjaman Mahasiswa ditolak.');
    }

    public function markAsBorrowed($id)
    {
        $peminjaman = Peminjaman::with('alat')->findOrFail($id);
        
        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()->with('error', 'Peminjaman harus disetujui dahulu.');
        }

        // Stock is already decremented when admin approves.
        // This method is kept as a fallback confirmation.
        return redirect()->back()->with('success', 'Status peminjaman sudah DIPINJAM.');
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

    // ===================================================
    // KELOLA KEPERLUAN
    // ===================================================

    private function getKeperluanOptions(): array
    {
        $path = storage_path('app/keperluan.json');

        if (!file_exists($path)) {
            $default = ['Penelitian', 'Tugas Harian', 'Pengabdian', 'Praktikum', 'Perkuliahan'];
            file_put_contents($path, json_encode($default));
            return $default;
        }

        $data = json_decode(file_get_contents($path), true);

        return is_array($data) ? $data : [];
    }

    public function addKeperluan(Request $request)
    {
        $request->validate([
            'keperluan' => 'required|string|max:100',
        ]);

        $options = $this->getKeperluanOptions();
        $newOption = trim($request->keperluan);

        // Prevent duplicates
        if (in_array($newOption, $options)) {
            return redirect()->back()->with('error', 'Keperluan "' . $newOption . '" sudah ada.');
        }

        $options[] = $newOption;
        file_put_contents(storage_path('app/keperluan.json'), json_encode($options));

        return redirect()->back()->with('success', 'Keperluan "' . $newOption . '" berhasil ditambahkan.');
    }

    public function removeKeperluan(Request $request)
    {
        $request->validate([
            'keperluan' => 'required|string',
        ]);

        $options = $this->getKeperluanOptions();
        $options = array_values(array_filter($options, fn($o) => $o !== $request->keperluan));
        file_put_contents(storage_path('app/keperluan.json'), json_encode($options));

        return redirect()->back()->with('success', 'Keperluan berhasil dihapus.');
    }

}
