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
        $userProdi = $user->program_studi;
        $prodiShort = str_contains($userProdi, 'D3') ? 'D3' : 'D4';

        // Base query for this prodi's kaprodi-relevant loans
        $baseQuery = Peminjaman::whereJsonContains('required_approvals', 'kaprodi')
            ->whereHas('user', function ($u) use ($prodiShort) {
                $u->where('program_studi', 'like', "%{$prodiShort}%");
            });

        // Stats count
        $stats = [
            'pending' => (clone $baseQuery)->where('status', 'pending')->whereNotNull('kalab_approved_by')->count(),
            'dipinjam' => (clone $baseQuery)->where('status', 'dipinjam')->count(),
            'selesai' => (clone $baseQuery)->where('status', 'selesai')->count(),
            'overdue' => (clone $baseQuery)->where('status', 'dipinjam')
                ->where('tanggal_kembali', '<', now()->toDateString())
                ->count(),
        ];

        // Fetch recent pending approvals
        $pending_approvals = (clone $baseQuery)
            ->with(['user', 'alat'])
            ->where('status', 'pending')
            ->whereNotNull('kalab_approved_by')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Fetch currently borrowed items (all for monitoring)
        $active_borrowings = (clone $baseQuery)
            ->with(['user', 'alat'])
            ->where('status', 'dipinjam')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('kaprodi.dashboard', compact('stats', 'pending_approvals', 'active_borrowings'));
    }
}
