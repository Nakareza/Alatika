<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'total_alat' => \App\Models\Alat::count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'pending_dosen' => Peminjaman::whereHas('user', function($q) {
                $q->where('role', 'dosen');
            })->where('status', 'pending')->count(),
            'overdue' => Peminjaman::where('status', 'dipinjam')
                ->where('tanggal_kembali', '<', now()->toDateString())
                ->count(),
        ];
        
        return view('kalab.dashboard', compact('stats'));
    }
}
