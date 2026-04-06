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
        // Real-time stats
        $stats = [
            'total'      => Peminjaman::count(),
            'pending'    => Peminjaman::where('status', 'pending')->count(),
            'dipinjam'   => Peminjaman::where('status', 'dipinjam')->count(),
            'selesai'    => Peminjaman::where('status', 'selesai')->count(),
            'ditolak'    => Peminjaman::where('status', 'ditolak')->count(),
            'overdue'    => Peminjaman::where('status', 'dipinjam')
                               ->where('tanggal_kembali', '<', now()->startOfDay())
                               ->count(),
        ];

        // Alat inventory stats
        $alatStats = [
            'total_alat'     => Alat::sum('stok_total'),
            'tersedia'       => Alat::sum('stok_tersedia'),
            'total_mahasiswa' => User::where('role', 'mahasiswa')->count(),
        ];

        // 10 peminjaman terbaru for the recent table
        $recentPeminjaman = Peminjaman::with(['user', 'alat'])
            ->latest()
            ->take(10)
            ->get();

        // 5 aktivitas terbaru for activity feed
        $recentActivities = Peminjaman::with('user')
            ->latest('updated_at')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'alatStats', 'recentPeminjaman', 'recentActivities'));
    }
}
