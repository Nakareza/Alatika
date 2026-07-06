<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index()
    {
        // Filter query for all loans under Kalab's control
        $peminjamanQuery = Peminjaman::where(function ($q) {
            $q->whereHas('user', fn($u) => $u->where('role', 'dosen'))
              ->orWhere(function ($sub) {
                  $sub->whereHas('user', fn($u) => $u->where('role', 'mahasiswa'))
                      ->whereHas('alat', fn($a) => $a->whereNotNull('program_studi'));
              });
        });

        // 1. Stats
        $stats = [
            'total' => (clone $peminjamanQuery)->count(),
            'dipinjam' => (clone $peminjamanQuery)->where('status', 'dipinjam')->count(),
            'selesai' => (clone $peminjamanQuery)->where('status', 'selesai')->count(),
            'ditolak' => (clone $peminjamanQuery)->where('status', 'ditolak')->count(),
            'alat' => Alat::whereNotNull('program_studi')->count(),
        ];

        // 2. Grafik (monthly counts for the last 6 months)
        $grafik = collect();
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $start = (clone $monthDate)->startOfMonth();
            $end = (clone $monthDate)->endOfMonth();

            $total = (clone $peminjamanQuery)
                ->whereBetween('created_at', [$start, $end])
                ->count();

            $grafik->push((object)[
                'bulan' => $monthDate->month,
                'total' => $total,
            ]);
        }

        // 3. Status Distribusi
        $statusList = ['pending', 'dipinjam', 'selesai', 'ditolak'];
        $statusDistribusi = collect();
        foreach ($statusList as $status) {
            $total = (clone $peminjamanQuery)->where('status', $status)->count();
            $statusDistribusi->push((object)[
                'status' => $status,
                'total' => $total,
            ]);
        }

        // 4. Top Alat (top tools borrowed)
        $topAlat = Peminjaman::with('alat')
            ->where(function ($q) {
                $q->whereHas('user', fn($u) => $u->where('role', 'dosen'))
                  ->orWhere(function ($sub) {
                      $sub->whereHas('user', fn($u) => $u->where('role', 'mahasiswa'))
                          ->whereHas('alat', fn($a) => $a->whereNotNull('program_studi'));
                  });
            })
            ->selectRaw('alat_id, count(*) as total_pinjam')
            ->groupBy('alat_id')
            ->orderByDesc('total_pinjam')
            ->take(5)
            ->get();

        // 5. Ringkasan Bulanan
        $ringkasanBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $start = now()->subMonths($i)->startOfMonth();
            $end = now()->subMonths($i)->endOfMonth();
            $bulanan = (clone $peminjamanQuery)->whereBetween('created_at', [$start, $end]);

            $ringkasanBulanan[] = [
                'bulan'     => \Carbon\Carbon::parse($start)->translatedFormat('F Y'),
                'pengajuan' => (clone $bulanan)->count(),
                'disetujui' => (clone $bulanan)->whereIn('status', ['dipinjam', 'selesai'])->count(),
                'ditolak'   => (clone $bulanan)->where('status', 'ditolak')->count(),
                'selesai'   => (clone $bulanan)->where('status', 'selesai')->count(),
            ];
        }

        return view('kalab.laporan.index', compact(
            'stats',
            'grafik',
            'statusDistribusi',
            'topAlat',
            'ringkasanBulanan'
        ));
    }

    public function exportCsv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="laporan_peminjaman_' . date('Y-m-d') . '.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $peminjamans = Peminjaman::with(['user', 'alat'])
            ->where(function ($q) {
                $q->whereHas('user', fn($u) => $u->where('role', 'dosen'))
                  ->orWhere(function ($sub) {
                      $sub->whereHas('user', fn($u) => $u->where('role', 'mahasiswa'))
                          ->whereHas('alat', fn($a) => $a->whereNotNull('program_studi'));
                  });
            })
            ->latest()
            ->get();

        $callback = function () use ($peminjamans) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 Excel interpretation
            fputs($file, "\xEF\xBB\xBF");
            
            // CSV Headers
            fputcsv($file, [
                'Kode Peminjaman',
                'Nama Peminjam',
                'Role Peminjam',
                'NIM/NIP',
                'Nama Alat',
                'Kode Alat',
                'Jumlah',
                'Tanggal Pinjam',
                'Tanggal Kembali',
                'Keperluan',
                'Status'
            ]);

            foreach ($peminjamans as $p) {
                fputcsv($file, [
                    $p->kode_peminjaman,
                    $p->user->name,
                    ucfirst($p->user->role),
                    $p->user->nim ?? $p->user->nip ?? '-',
                    $p->alat->nama,
                    $p->alat->kode,
                    $p->jumlah,
                    $p->tanggal_pinjam->format('Y-m-d'),
                    $p->tanggal_kembali->format('Y-m-d'),
                    $p->keperluan,
                    $p->status_label
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
