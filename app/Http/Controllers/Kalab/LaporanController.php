<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        // Kalab hanya menangani peminjaman DOSEN
        $dsn = Peminjaman::whereHas('user', fn($q) => $q->where('role', 'dosen'));

        $stats = [
            'total_peminjaman' => (clone $dsn)->count(),
            'total_dipinjam' => (clone $dsn)->where('status', 'dipinjam')->sum('jumlah'),
            'total_selesai' => (clone $dsn)->where('status', 'selesai')->count(),
            'total_ditolak' => (clone $dsn)->where('status', 'ditolak')->count(),
            'total_pending' => (clone $dsn)->where('status', 'pending')->count(),
            'overdue' => (clone $dsn)->where('status', 'dipinjam')
                ->where('tanggal_kembali', '<', now()->toDateString())
                ->count(),
        ];

        // Alat stats
        $alatStats = [
            'total_alat' => Alat::count(),
            'total_stok' => Alat::sum('stok_total'),
            'total_tersedia' => Alat::sum('stok_tersedia'),
            'maintenance' => Alat::where('status', 'maintenance')->count(),
        ];

        // Dosen aktif (dosen yang pernah meminjam)
        $dosenAktif = User::where('role', 'dosen')
            ->whereHas('peminjaman')
            ->count();

        // Top alat yang paling sering dipinjam dosen
        $topAlat = Peminjaman::with('alat')
            ->whereHas('user', fn($q) => $q->where('role', 'dosen'))
            ->selectRaw('alat_id, count(*) as total_pinjam')
            ->groupBy('alat_id')
            ->orderByDesc('total_pinjam')
            ->take(5)
            ->get();

        // Ringkasan bulanan (6 bulan terakhir)
        $ringkasanBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();

            $bulanan = Peminjaman::whereHas('user', fn($q) => $q->where('role', 'dosen'))
                ->whereBetween('created_at', [$start, $end]);

            $ringkasanBulanan[] = [
                'bulan' => $start->translatedFormat('F Y'),
                'pengajuan' => (clone $bulanan)->count(),
                'disetujui' => (clone $bulanan)->whereIn('status', ['dipinjam', 'selesai'])->count(),
                'ditolak' => (clone $bulanan)->where('status', 'ditolak')->count(),
                'selesai' => (clone $bulanan)->where('status', 'selesai')->count(),
            ];
        }

        return view('kalab.laporan.index', compact(
            'stats',
            'alatStats',
            'dosenAktif',
            'topAlat',
            'ringkasanBulanan'
        ));
    }
}
