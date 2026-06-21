<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    public function persetujuan(Request $request)
    {
        $query = Peminjaman::with(['user', 'alat'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'dosen');
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
        $dsn = Peminjaman::whereHas('user', fn($q) => $q->where('role', 'dosen'));
        $stats = [
            'pending' => (clone $dsn)->where('status', 'pending')->count(),
            'dipinjam' => (clone $dsn)->where('status', 'dipinjam')->count(),
            'selesai' => (clone $dsn)->where('status', 'selesai')->count(),
            'total_pengajuan' => (clone $dsn)->count(),
        ];

        return view(
            'kalab.persetujuan.index',
            compact('peminjaman', 'stats')
        );
    }

    public function riwayat()
    {
        // Ka Lab hanya melihat riwayat peminjaman DOSEN
        $peminjaman = Peminjaman::with(['user', 'alat'])
            ->whereHas('user', function ($q) {
                $q->where('role', 'dosen');
            })
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('kalab.riwayat.index', compact('peminjaman'));
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'alat',
            'approvedBy'
        ])->findOrFail($id);

        return view(
            'kalab.peminjaman.show',
            compact('peminjaman')
        );
    }

    public function approve($id, TelegramService $telegram)
    {
        $peminjaman = Peminjaman::with(['user', 'alat'])->findOrFail($id);

        // Guard: kalab hanya approve dosen
        if ($peminjaman->user->role !== 'dosen') {
            return redirect()->back()->with('error', 'Kalab hanya dapat menyetujui peminjaman dosen.');
        }

        // Check stock availability before approving
        $alat = $peminjaman->alat;
        if ($alat->stok_tersedia < $peminjaman->jumlah) {
            return redirect()->back()->with('error', 'Stok alat "' . $alat->nama . '" tidak mencukupi (tersedia: ' . $alat->stok_tersedia . ', diminta: ' . $peminjaman->jumlah . ').');
        }

        // Decrement stock upon approval
        $alat->stok_tersedia -= $peminjaman->jumlah;
        $alat->save();

        // Kalab langsung setujui (sole approver untuk dosen)
        $peminjaman->update([
            'kalab_approved_by' => Auth::id(),
            'kalab_approved_at' => now(),
            'status' => 'dipinjam',
        ]);

        $telegram->notifyPeminjamanApproved($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'jumlah' => $peminjaman->jumlah,
            'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
            'approver_role' => 'Kepala Lab',
        ]);

        return redirect()->back()->with('success', 'Peminjaman Dosen disetujui. Status: Dipinjam.');
    }

    public function reject(Request $request, $id, TelegramService $telegram)
    {
        $request->validate(['alasan' => 'required|string']);

        $peminjaman = Peminjaman::with('user')->findOrFail($id);

        // Guard: kalab hanya reject dosen
        if ($peminjaman->user->role !== 'dosen') {
            return redirect()->back()->with('error', 'Kalab hanya dapat menolak peminjaman dosen.');
        }

        $peminjaman->update([
            'status' => 'ditolak',
            'rejected_reason' => $request->alasan,
            'kalab_approved_by' => Auth::id(),
            'kalab_approved_at' => now(),
        ]);

        $telegram->notifyPeminjamanRejected($peminjaman->user, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $peminjaman->alat->nama,
            'alasan' => $request->alasan,
        ]);

        return redirect()->back()->with('success', 'Peminjaman Dosen ditolak.');
    }

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
            ->whereHas('user', fn($q) => $q->where('role', 'dosen'))
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

            // Decrement stock upon approval
            $alat->stok_tersedia -= $peminjaman->jumlah;
            $alat->save();

            $peminjaman->update([
                'kalab_approved_by' => Auth::id(),
                'kalab_approved_at' => now(),
                'status' => 'dipinjam',
            ]);

            $telegram->notifyPeminjamanApproved($peminjaman->user, [
                'kode' => $peminjaman->kode_peminjaman,
                'alat' => $peminjaman->alat->nama,
                'jumlah' => $peminjaman->jumlah,
                'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                'approver_role' => 'Kepala Lab',
            ]);

            $approvedCount++;
        }

        $message = "$approvedCount Peminjaman Dosen berhasil disetujui. Status: Dipinjam.";
        if (!empty($failedMessages)) {
            $message .= ' Gagal: ' . implode(', ', $failedMessages) . '.';
        }

        return redirect()->back()->with($failedMessages ? 'error' : 'success', $message);
    }
}
