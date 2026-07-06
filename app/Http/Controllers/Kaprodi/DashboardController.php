<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Stats count
        $stats = [
            'pending' => Peminjaman::where('status', 'pending')
                ->whereHas('user', function ($q) {
                    $q->where('role', 'dosen');
                })
                ->whereHas('alat', function ($q) {
                    $q->whereNotNull('program_studi');
                })
                ->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'selesai' => Peminjaman::where('status', 'selesai')->count(),
            'overdue' => Peminjaman::where('status', 'dipinjam')
                ->where('tanggal_kembali', '<', now()->toDateString())
                ->count(),
        ];

        // Fetch recent pending approvals (only Dosen loans for tools requiring Kaprodi approval)
        $pending_approvals = Peminjaman::with(['user', 'alat'])
            ->where('status', 'pending')
            ->whereHas('user', function ($q) {
                $q->where('role', 'dosen');
            })
            ->whereHas('alat', function ($q) {
                $q->whereNotNull('program_studi');
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch currently borrowed items (all for monitoring)
        $active_borrowings = Peminjaman::with(['user', 'alat'])
            ->where('status', 'dipinjam')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('kaprodi.dashboard', compact('stats', 'pending_approvals', 'active_borrowings'));
    }
}
