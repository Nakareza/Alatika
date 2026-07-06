<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    /**
     * Halaman persetujuan - menampilkan peminjaman yang butuh approval kaprodi
     */
    public function persetujuan(Request $request)
    {
        $query = Peminjaman::with(['user', 'alat']);

        // Kaprodi only approves pending loans for Dosen where the tool has a program study
        $query->where(function ($q) {
            $q->where('status', '!=', 'pending')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'pending')
                      ->whereHas('user', function ($u) {
                          $u->where('role', 'dosen');
                      })
                      ->whereHas('alat', function ($a) {
                          $a->whereNotNull('program_studi');
                      });
              });
        });

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('kode_peminjaman', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('alat', function ($a) use ($search) {
                    $a->where('nama', 'like', "%{$search}%");
                });
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter Periode
        if ($request->filled('periode')) {
            switch ($request->periode) {
                case 'hari_ini':
                    $query->whereDate('created_at', now()->toDateString());
                    break;
                case 'minggu_ini':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'bulan_ini':
                    $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    break;
            }
        }

        $peminjaman = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // Statistik
        $stats = [
            'pending' => Peminjaman::where('status', 'pending')
                ->whereHas('user', fn($u) => $u->where('role', 'dosen'))
                ->whereHas('alat', fn($a) => $a->whereNotNull('program_studi'))
                ->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'selesai' => Peminjaman::where('status', 'selesai')->count(),
            'total_pengajuan' => Peminjaman::count(),
        ];

        return view(
            'kaprodi.persetujuan.index',
            compact('peminjaman', 'stats')
        );
    }

    /**
     * Riwayat peminjaman
     */
    public function riwayat(Request $request)
    {
        $peminjaman = Peminjaman::with(['user', 'alat'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('kaprodi.riwayat.index', compact('peminjaman'));
    }

    /**
     * Approve peminjaman oleh Kaprodi
     */
    public function approve(Request $request, $id, TelegramService $telegram)
    {
        $peminjaman = Peminjaman::with(['user', 'alat'])->findOrFail($id);

        // Kaprodi hanya approve yang masih pending
        if ($peminjaman->status !== 'pending') {
            return redirect()->back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        // Check stock availability before approving
        $alat = $peminjaman->alat;
        if ($alat->stok_tersedia < $peminjaman->jumlah) {
            return redirect()->back()->with('error', 'Stok alat "' . $alat->nama . '" tidak mencukupi (tersedia: ' . $alat->stok_tersedia . ', diminta: ' . $peminjaman->jumlah . ').');
        }

        // Set Kaprodi approved fields
        $peminjaman->update([
            'kaprodi_approved_by' => Auth::id(),
            'kaprodi_approved_at' => now(),
        ]);

        // Check if the Kalab has already approved
        $otherApproved = $peminjaman->kalab_approved_by !== null;
        $otherRole = 'Kepala Lab';

        if ($otherApproved) {
            // Decrement stock upon final approval
            $alat->stok_tersedia -= $peminjaman->jumlah;
            $alat->save();

            $peminjaman->update(['status' => 'dipinjam']);

            $telegram->notifyPeminjamanApproved($peminjaman->user, [
                'kode' => $peminjaman->kode_peminjaman,
                'alat' => $peminjaman->alat->nama,
                'jumlah' => $peminjaman->jumlah,
                'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                'approver_role' => $otherRole . ' dan Kaprodi',
            ]);

            return redirect()->back()->with('success', 'Peminjaman disetujui oleh Kaprodi. Status: Dipinjam (Disetujui oleh ' . $otherRole . ' dan Kaprodi).');
        } else {
            return redirect()->back()->with('success', 'Peminjaman disetujui oleh Kaprodi. Menunggu persetujuan dari ' . $otherRole . '.');
        }
    }

    /**
     * Reject peminjaman oleh Kaprodi
     */
    public function reject(Request $request, $id, TelegramService $telegram)
    {
        $request->validate(['alasan' => 'required|string']);

        $peminjaman = Peminjaman::with('user')->findOrFail($id);

        if ($peminjaman->status !== 'pending') {
            return redirect()->back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
        }

        $peminjaman->update([
            'status' => 'ditolak',
            'rejected_reason' => $request->alasan,
            'kaprodi_approved_by' => Auth::id(),
            'kaprodi_approved_at' => now(),
        ]);

        $telegram->notifyPeminjamanRejected($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'alasan' => $request->alasan,
        ]);

        return redirect()->back()->with('success', 'Peminjaman ditolak oleh Kaprodi.');
    }

    /**
     * Bulk approve peminjaman
     */
    public function bulkApprove(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'peminjaman_ids' => 'required|array',
            'peminjaman_ids.*' => 'exists:peminjaman,id',
        ]);

        $ids = $request->peminjaman_ids;
        $peminjamans = Peminjaman::with(['user', 'alat'])
            ->whereIn('id', $ids)
            ->where('status', 'pending')
            ->get();

        $approvedCount = 0;
        $failedMessages = [];

        foreach ($peminjamans as $peminjaman) {
            $alat = $peminjaman->alat;

            // Check stock availability
            if ($alat->stok_tersedia < $peminjaman->jumlah) {
                $failedMessages[] = '"' . $alat->nama . '" stok tidak mencukupi (tersedia: ' . $alat->stok_tersedia . ')';
                continue;
            }

            // Set Kaprodi approved fields
            $peminjaman->update([
                'kaprodi_approved_by' => Auth::id(),
                'kaprodi_approved_at' => now(),
            ]);

            // Check if Kalab has already approved
            $otherApproved = $peminjaman->kalab_approved_by !== null;
            $otherRole = 'Kepala Lab';

            if ($otherApproved) {
                // Decrement stock upon final approval
                $alat->stok_tersedia -= $peminjaman->jumlah;
                $alat->save();

                $peminjaman->update(['status' => 'dipinjam']);

                $telegram->notifyPeminjamanApproved($peminjaman->user, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $peminjaman->alat->nama,
                    'jumlah' => $peminjaman->jumlah,
                    'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                    'approver_role' => $otherRole . ' dan Kaprodi',
                ]);

                $approvedCount++;
            }
        }

        $message = "$approvedCount Peminjaman berhasil disetujui sepenuhnya oleh Kaprodi.";
        if (!empty($failedMessages)) {
            $message .= ' Gagal: ' . implode(', ', $failedMessages) . '.';
        }

        return redirect()->back()->with($failedMessages ? 'error' : 'success', $message);
    }
}
