<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
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

        return view('dosen.dashboard', compact('stats', 'recent', 'statusSummary'));
    }
}
