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
        $userProdi = auth()->user()->program_studi;
        $prodiShort = str_contains($userProdi, 'D3') ? 'D3' : 'D4';

        $query = Peminjaman::with(['user', 'alat'])
            ->where(function ($q) use ($prodiShort) {
                $q->whereHas('user', function ($u) use ($prodiShort) {
                    $u->where('program_studi', 'like', "%{$prodiShort}%");
                })->orWhere(function ($sub) use ($prodiShort) {
                    $sub->whereNull('user_id')
                        ->whereHas('alat', function ($a) use ($prodiShort) {
                            $a->where('program_studi', 'like', "%{$prodiShort}%");
                        });
                });
            });

        // Kaprodi only sees/approves pending loans that require Kaprodi approval and have been approved by Kalab,
        // or non-pending loans.
        $query->where(function ($q) {
            $q->where('status', '!=', 'pending')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'pending')
                      ->whereJsonContains('required_approvals', 'kaprodi')
                      ->whereNotNull('kalab_approved_by');
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
        $statsQuery = Peminjaman::where(function ($q) use ($prodiShort) {
            $q->whereHas('user', function ($u) use ($prodiShort) {
                $u->where('program_studi', 'like', "%{$prodiShort}%");
            })->orWhere(function ($sub) use ($prodiShort) {
                $sub->whereNull('user_id')
                    ->whereHas('alat', function ($a) use ($prodiShort) {
                        $a->where('program_studi', 'like', "%{$prodiShort}%");
                    });
            });
        });

        $stats = [
             'pending' => (clone $statsQuery)->where('status', 'pending')
                ->whereJsonContains('required_approvals', 'kaprodi')
                ->whereNotNull('kalab_approved_by')
                ->count(),
            'dipinjam' => (clone $statsQuery)->where('status', 'dipinjam')->count(),
            'selesai' => (clone $statsQuery)->where('status', 'selesai')->count(),
            'total_pengajuan' => (clone $statsQuery)->count(),
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
        $userProdi = auth()->user()->program_studi;
        $prodiShort = str_contains($userProdi, 'D3') ? 'D3' : 'D4';

        $peminjaman = Peminjaman::with(['user', 'alat'])
            ->where(function ($q) use ($prodiShort) {
                $q->whereHas('user', function ($u) use ($prodiShort) {
                    $u->where('program_studi', 'like', "%{$prodiShort}%");
                })->orWhere(function ($sub) use ($prodiShort) {
                    $sub->whereNull('user_id')
                        ->whereHas('alat', function ($a) use ($prodiShort) {
                            $a->where('program_studi', 'like', "%{$prodiShort}%");
                        });
                });
            })
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

        if ($peminjaman->required_approvals !== null) {
            if (!in_array('kaprodi', $peminjaman->required_approvals)) {
                return redirect()->back()->with('error', 'Akses ditolak. Peminjaman ini tidak memerlukan persetujuan Kaprodi.');
            }
        }

        // Check stock availability before approving
        $alat = $peminjaman->alat;
        if ($alat->stok_tersedia < $peminjaman->jumlah) {
            return redirect()->back()->with('error', 'Stok alat "' . $alat->nama . '" tidak mencukupi (tersedia: ' . $alat->stok_tersedia . ', diminta: ' . $peminjaman->jumlah . ').');
        }

        // Set Kaprodi approved fields
        $updateData = [
            'kaprodi_approved_by' => Auth::id(),
            'kaprodi_approved_at' => now(),
        ];

        // Custom approvals logic
        if ($peminjaman->required_approvals !== null) {
            $peminjaman->update($updateData);

            // Check if all required approvals are met
            $isFullyApproved = true;
            $approverNames = [];

            if (in_array('admin', $peminjaman->required_approvals)) {
                $approverNames[] = 'Admin/Teknisi';
                if ($peminjaman->admin_approved_by === null) $isFullyApproved = false;
            }
            if (in_array('kalab', $peminjaman->required_approvals)) {
                $approverNames[] = 'Kepala Lab';
                if ($peminjaman->kalab_approved_by === null) $isFullyApproved = false;
            }
            if (in_array('kaprodi', $peminjaman->required_approvals)) {
                $approverNames[] = 'Kaprodi';
                if ($peminjaman->kaprodi_approved_by === null) $isFullyApproved = false;
            }

            $approverList = implode(' dan ', $approverNames);

            if ($isFullyApproved) {
                $alat->decrement('stok_tersedia', $peminjaman->jumlah);
                $peminjaman->update(['status' => 'dipinjam']);

                $telegram->notifyPeminjamanApproved($peminjaman->user, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $alat->nama,
                    'jumlah' => $peminjaman->jumlah,
                    'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                    'approver_role' => $approverList,
                ]);

                return redirect()->back()->with('success', "Peminjaman disetujui secara final (Disetujui oleh {$approverList}). Status: Dipinjam.");
            } else {
                // Find who is next to approve
                $pendingApprovers = [];
                if (in_array('admin', $peminjaman->required_approvals) && $peminjaman->admin_approved_by === null) {
                    $pendingApprovers[] = 'Admin';
                    // Notify Admin
                    $admins = \App\Models\User::where('role', 'admin')->whereNotNull('telegram_chat_id')->get();
                    foreach ($admins as $admin) {
                        $telegram->notifyNewRequest($admin, [
                            'peminjam_nama' => $peminjaman->user->name,
                            'peminjam_role' => $peminjaman->user->role,
                            'alat' => $alat->nama,
                            'jumlah' => $peminjaman->jumlah,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }
                if (in_array('kalab', $peminjaman->required_approvals) && $peminjaman->kalab_approved_by === null) {
                    $pendingApprovers[] = 'Kepala Lab';
                    // Notify Kalab
                    $kalabs = \App\Models\User::where('role', 'kalab')->whereNotNull('telegram_chat_id')->get();
                    foreach ($kalabs as $kalab) {
                        $telegram->notifyNewRequest($kalab, [
                            'peminjam_nama' => $peminjaman->user->name,
                            'peminjam_role' => $peminjaman->user->role,
                            'alat' => $alat->nama,
                            'jumlah' => $peminjaman->jumlah,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }
                if (in_array('kaprodi', $peminjaman->required_approvals) && $peminjaman->kaprodi_approved_by === null) {
                    $pendingApprovers[] = 'Kaprodi';
                }

                $pendingList = implode(', ', $pendingApprovers);
                return redirect()->back()->with('success', "Peminjaman disetujui oleh Kaprodi. Menunggu persetujuan dari: {$pendingList}.");
            }
        }

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
                'alat' => $peminjaman->item_name,
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

        if ($peminjaman->user) {
            $telegram->notifyPeminjamanRejected($peminjaman->user, [
                'kode' => $peminjaman->kode_peminjaman,
                'alat' => $peminjaman->item_name,
                'alasan' => $request->alasan,
            ]);
        }

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
        $userProdi = auth()->user()->program_studi;
        $prodiShort = str_contains($userProdi, 'D3') ? 'D3' : 'D4';

        $peminjamans = Peminjaman::with(['user', 'alat'])
            ->whereIn('id', $ids)
            ->where('status', 'pending')
            ->where(function ($q) use ($prodiShort) {
                $q->whereHas('user', function ($u) use ($prodiShort) {
                    $u->where('program_studi', 'like', "%{$prodiShort}%");
                })->orWhere(function ($sub) use ($prodiShort) {
                    $sub->whereNull('user_id')
                        ->whereHas('alat', function ($a) use ($prodiShort) {
                            $a->where('program_studi', 'like', "%{$prodiShort}%");
                        });
                });
            })
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('user', fn($u) => $u->where('role', 'dosen'))
                        ->whereHas('alat', fn($a) => $a->whereNotNull('program_studi'))
                        ->whereNotNull('kalab_approved_by');
                })->orWhereJsonContains('required_approvals', 'kaprodi');
            })
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
                    'alat' => $peminjaman->item_name,
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
