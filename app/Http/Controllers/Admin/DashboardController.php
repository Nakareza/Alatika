<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        // Admin hanya menangani peminjaman MAHASISWA
        $mhs = Peminjaman::whereHas('user', fn($q) => $q->where('role', 'mahasiswa'));

        $stats = [
            'total'      => (clone $mhs)->count(),
            'pending'    => (clone $mhs)->where('status', 'pending')->count(),
            'dipinjam'   => (clone $mhs)->where('status', 'dipinjam')->count(),
            'selesai'    => (clone $mhs)->where('status', 'selesai')->count(),
            'ditolak'    => (clone $mhs)->where('status', 'ditolak')->count(),
            'overdue'    => (clone $mhs)->where('status', 'dipinjam')
                               ->where('tanggal_kembali', '<', now()->startOfDay())
                               ->count(),
        ];

        // Alat inventory stats
        $alatStats = [
            'total_alat'     => Alat::sum('stok_total'),
            'tersedia'       => Alat::sum('stok_tersedia'),
            'total_mahasiswa' => User::where('role', 'mahasiswa')->count(),
        ];

        // 10 peminjaman mahasiswa terbaru
        $recentPeminjaman = Peminjaman::with(['user', 'alat'])
            ->whereHas('user', fn($q) => $q->where('role', 'mahasiswa'))
            ->latest()
            ->take(10)
            ->get();

        // 5 aktivitas mahasiswa terbaru
        $recentActivities = Peminjaman::with('user')
            ->whereHas('user', fn($q) => $q->where('role', 'mahasiswa'))
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'alatStats', 'recentPeminjaman', 'recentActivities'));
    }
}
