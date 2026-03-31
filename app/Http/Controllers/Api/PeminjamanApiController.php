<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PeminjamanApiController extends Controller
{
    /**
     * Get real-time stats for the admin dashboard polling
     */
    public function stats()
    {
        $pending = Peminjaman::where('status', 'pending')->count();
        $dipinjam = Peminjaman::where('status', 'dipinjam')->count();
        $menungguVerifikasi = Peminjaman::where('status', 'menunggu_verifikasi')->count();

        // Also get the latest 5 activities
        $recent = Peminjaman::with(['user', 'alat'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'user_name' => $p->user->name,
                    'user_initial' => substr($p->user->name, 0, 2),
                    'alat_nama' => $p->alat->nama,
                    'status' => $p->status,
                    'status_label' => $p->status_label,
                    'time_ago' => $p->updated_at->diffForHumans(),
                ];
            });

        return response()->json([
            'stats' => [
                'pending' => $pending,
                'dipinjam' => $dipinjam,
                'menunggu_verifikasi' => $menungguVerifikasi,
            ],
            'recent' => $recent,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get list of pending verifications (returned items)
     */
    public function pendingVerifications()
    {
        $returns = Peminjaman::with(['user', 'alat'])
            ->where('status', 'menunggu_verifikasi')
            ->orderBy('tanggal_dikembalikan', 'desc')
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'kode' => $p->kode_peminjaman,
                    'user_name' => $p->user->name,
                    'alat_nama' => $p->alat->nama,
                    'foto_url' => $p->foto_bukti_url,
                    'tanggal_kembali' => $p->tanggal_dikembalikan->format('d M Y H:i'),
                    'time_ago' => $p->tanggal_dikembalikan->diffForHumans()
                ];
            });

        return response()->json([
            'returns' => $returns,
            'count' => $returns->count(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
