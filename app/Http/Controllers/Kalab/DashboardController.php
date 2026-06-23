<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Ka Lab hanya menangani peminjaman DOSEN
        $dsn = Peminjaman::whereHas('user', fn($q) => $q->where('role', 'dosen'));
        
        $stats = [
            'total_alat' => \App\Models\Alat::count(),
            'tersedia' => \App\Models\Alat::sum('stok_tersedia'),
            'dipinjam' => (clone $dsn)->where('status', 'dipinjam')->sum('jumlah'),
            'pending_dosen' => (clone $dsn)->where('status', 'pending')->count(),
            'selesai' => (clone $dsn)->where('status', 'selesai')->count(),
            'overdue' => (clone $dsn)->where('status', 'dipinjam')
                ->where('tanggal_kembali', '<', now()->toDateString())
                ->count(),
            'rusak' => 0,
        ];

        // Fetch recent pending approvals (dosen only)
        $pending_approvals = Peminjaman::with(['user', 'alat'])
            ->whereHas('user', function($q) {
                $q->where('role', 'dosen');
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch recent activities (dosen only)
        $recent_activities = Peminjaman::with(['user', 'alat'])
            ->whereHas('user', fn($q) => $q->where('role', 'dosen'))
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        
        return view('kalab.dashboard', compact('stats', 'pending_approvals', 'recent_activities'));
    }
}
