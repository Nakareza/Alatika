<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display mahasiswa dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        $title = 'Dashboard';
        $breadcrumbs = [];
        
        $stats = [
            'total' => $user->peminjaman()->count(),
            'dipinjam' => $user->peminjaman()->where('status', 'dipinjam')->count(),
            'selesai' => $user->peminjaman()->where('status', 'selesai')->count(),
            'ditolak' => $user->peminjaman()->where('status', 'ditolak')->count(),
        ];
        
        $recent = $user->peminjaman()->with('alat')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        $statusSummary = [
            'pending' => $user->peminjaman()->where('status', 'pending')->count(),
            'dipinjam' => $user->peminjaman()->where('status', 'dipinjam')->count(),
            'selesai' => $user->peminjaman()->where('status', 'selesai')->count(),
        ];

        return view('mahasiswa.dashboard', compact('title', 'breadcrumbs', 'stats', 'recent', 'statusSummary'));
    }
}
