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
        
        $stats = [
            'total_alat' => \App\Models\Alat::count(),
            'tersedia' => \App\Models\Alat::sum('stok_tersedia'),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'pending_dosen' => Peminjaman::whereHas('user', function($q) {
                $q->where('role', 'dosen');
            })->where('status', 'pending')->count(),
            'disetujui' => Peminjaman::where('status', 'disetujui')->whereMonth('created_at', now()->month)->count(),
            'overdue' => Peminjaman::where('status', 'dipinjam')
                ->where('tanggal_kembali', '<', now()->toDateString())
                ->count(),
            'rusak' => 0, // Placeholder, usually there's a status for this
        ];

        // Fetch recent pending approvals
        $pending_approvals = Peminjaman::with(['user', 'alat'])
            ->whereHas('user', function($q) {
                $q->where('role', 'dosen'); // Ka Lab mostly approves Dosen
            })
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch recent activities (all actions)
        $recent_activities = Peminjaman::with(['user', 'alat'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();
        
        return view('kalab.dashboard', compact('stats', 'pending_approvals', 'recent_activities'));
    }
}
