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
            ->where(function ($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('role', 'dosen');
                })->orWhere(function ($sub) {
                    $sub->whereHas('user', function ($u) {
                        $u->where('role', 'mahasiswa');
                    })->whereHas('alat', function ($a) {
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
        $statsQuery = Peminjaman::where(function ($q) {
            $q->whereHas('user', function ($u) {
                $u->where('role', 'dosen');
            })->orWhere(function ($sub) {
                $sub->whereHas('user', function ($u) {
                    $u->where('role', 'mahasiswa');
                })->whereHas('alat', function ($a) {
                    $a->whereNotNull('program_studi');
                });
            });
        });

        $stats = [
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'dipinjam' => (clone $statsQuery)->where('status', 'dipinjam')->count(),
            'selesai' => (clone $statsQuery)->where('status', 'selesai')->count(),
            'total_pengajuan' => (clone $statsQuery)->count(),
        ];

        return view(
            'kalab.persetujuan.index',
            compact('peminjaman', 'stats')
        );
    }

    public function riwayat(Request $request)
    {
        // Ka Lab melihat riwayat peminjaman DOSEN dan MAHASISWA alat khusus
        $peminjaman = Peminjaman::with(['user', 'alat'])
            ->where(function ($q) {
                $q->whereHas('user', function ($u) {
                    $u->where('role', 'dosen');
                })->orWhere(function ($sub) {
                    $sub->whereHas('user', function ($u) {
                        $u->where('role', 'mahasiswa');
                    })->whereHas('alat', function ($a) {
                        $a->whereNotNull('program_studi');
                    });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('kalab.riwayat.index', compact('peminjaman'));
    }

    public function show($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'alat',
        ])->findOrFail($id);

        return view('kalab.persetujuan.show', compact('peminjaman'));
    }

    public function approve(Request $request, $id, TelegramService $telegram)
    {
        $peminjaman = Peminjaman::with(['user', 'alat'])->findOrFail($id);

        // Guard: kalab hanya approve dosen ATAU mahasiswa dengan alat khusus
        if ($peminjaman->user->role !== 'dosen' && !($peminjaman->user->role === 'mahasiswa' && $peminjaman->alat->program_studi !== null)) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda tidak berwenang menyetujui peminjaman ini.');
        }

        // Check stock availability before approving
        $alat = $peminjaman->alat;
        if ($alat->stok_tersedia < $peminjaman->jumlah) {
            return redirect()->back()->with('error', 'Stok alat "' . $alat->nama . '" tidak mencukupi (tersedia: ' . $alat->stok_tersedia . ', diminta: ' . $peminjaman->jumlah . ').');
        }

        // Update keperluan if Kalab modified it
        $updateData = [
            'kalab_approved_by' => Auth::id(),
            'kalab_approved_at' => now(),
        ];

        if ($request->filled('keperluan')) {
            $newKeperluan = $request->input('keperluan');
            $updateData['keperluan'] = $newKeperluan;

            // Add new keperluan to the global list if it doesn't exist
            static::addKeperluanIfNew($newKeperluan);
        }

        // Case 1: Student borrowing a special tool (double approval: Admin + Kalab)
        if ($peminjaman->user->role === 'mahasiswa') {
            $peminjaman->update($updateData);

            if ($peminjaman->admin_approved_by !== null) {
                // Finalize approval: decrement stock, set status to dipinjam
                $alat->decrement('stok_tersedia', $peminjaman->jumlah);
                $peminjaman->update(['status' => 'dipinjam']);

                $telegram->notifyPeminjamanApproved($peminjaman->user, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $peminjaman->alat->nama,
                    'jumlah' => $peminjaman->jumlah,
                    'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                    'approver_role' => 'Admin dan Kepala Lab',
                ]);

                return redirect()->back()->with('success', 'Peminjaman Mahasiswa disetujui. Status: Dipinjam (Disetujui oleh Admin & Kepala Lab).');
            } else {
                return redirect()->back()->with('success', 'Peminjaman disetujui oleh Kepala Lab. Menunggu persetujuan dari Admin.');
            }
        }

        // Case 2: Dosen borrowing a prodi tool (double approval: Kalab + Kaprodi)
        if ($alat->program_studi !== null) {
            $peminjaman->update($updateData);

            // If kaprodi has already approved, finalize approval
            if ($peminjaman->kaprodi_approved_by !== null) {
                // Decrement stock upon approval
                $alat->stok_tersedia -= $peminjaman->jumlah;
                $alat->save();

                $peminjaman->update(['status' => 'dipinjam']);

                $telegram->notifyPeminjamanApproved($peminjaman->user, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $peminjaman->alat->nama,
                    'jumlah' => $peminjaman->jumlah,
                    'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                    'approver_role' => 'Kepala Lab dan Kaprodi',
                ]);

                return redirect()->back()->with('success', 'Peminjaman Dosen disetujui. Status: Dipinjam (Disetujui oleh Kepala Lab dan Kaprodi).');
            } else {
                // Notify Kaprodi to approve
                $kaprodis = User::where('role', 'kaprodi')->whereNotNull('telegram_chat_id')->get();
                foreach ($kaprodis as $kaprodi) {
                    $telegram->notifyNewRequest($kaprodi, [
                        'peminjam_nama' => $peminjaman->user->name,
                        'peminjam_role' => 'dosen',
                        'alat' => $alat->nama,
                        'jumlah' => $peminjaman->jumlah,
                        'kode' => $peminjaman->kode_peminjaman,
                    ]);
                }

                return redirect()->back()->with('success', 'Peminjaman disetujui oleh Kepala Lab. Menunggu persetujuan dari Kepala Program Studi.');
            }
        }

        // Standard single approval (decrements stock immediately)
        $alat->stok_tersedia -= $peminjaman->jumlah;
        $alat->save();

        $updateData['status'] = 'dipinjam';
        $peminjaman->update($updateData);

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

        // Guard: kalab hanya reject dosen atau mahasiswa alat khusus
        if ($peminjaman->user->role !== 'dosen' && !($peminjaman->user->role === 'mahasiswa' && $peminjaman->alat->program_studi !== null)) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda tidak berwenang menolak peminjaman ini.');
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

        $roleLabel = $peminjaman->user->role === 'dosen' ? 'Dosen' : 'Mahasiswa';
        return redirect()->back()->with('success', "Peminjaman {$roleLabel} ditolak.");
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
            ->where(function ($q) {
                $q->whereHas('user', fn($u) => $u->where('role', 'dosen'))
                  ->orWhere(function ($sub) {
                      $sub->whereHas('user', fn($u) => $u->where('role', 'mahasiswa'))
                          ->whereHas('alat', fn($a) => $a->whereNotNull('program_studi'));
                  });
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

            $updateData = [
                'kalab_approved_by' => Auth::id(),
                'kalab_approved_at' => now(),
            ];

            // Case 1: Student borrowing special tool (Admin + Kalab)
            if ($peminjaman->user->role === 'mahasiswa') {
                $peminjaman->update($updateData);

                if ($peminjaman->admin_approved_by !== null) {
                    $alat->stok_tersedia -= $peminjaman->jumlah;
                    $alat->save();

                    $peminjaman->update(['status' => 'dipinjam']);

                    $telegram->notifyPeminjamanApproved($peminjaman->user, [
                        'kode' => $peminjaman->kode_peminjaman,
                        'alat' => $peminjaman->alat->nama,
                        'jumlah' => $peminjaman->jumlah,
                        'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                        'approver_role' => 'Admin dan Kepala Lab',
                    ]);

                    $approvedCount++;
                }
                continue;
            }

            // Case 2: Dosen borrowing prodi tool (Kalab + Kaprodi)
            if ($alat->program_studi !== null) {
                $peminjaman->update($updateData);

                // If kaprodi has already approved, finalize approval
                if ($peminjaman->kaprodi_approved_by !== null) {
                    $alat->stok_tersedia -= $peminjaman->jumlah;
                    $alat->save();

                    $peminjaman->update(['status' => 'dipinjam']);

                    $telegram->notifyPeminjamanApproved($peminjaman->user, [
                        'kode' => $peminjaman->kode_peminjaman,
                        'alat' => $peminjaman->alat->nama,
                        'jumlah' => $peminjaman->jumlah,
                        'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                        'approver_role' => 'Kepala Lab dan Kaprodi',
                    ]);

                    $approvedCount++;
                } else {
                    // Notify Kaprodi to approve
                    $kaprodis = User::where('role', 'kaprodi')->whereNotNull('telegram_chat_id')->get();
                    foreach ($kaprodis as $kaprodi) {
                        $telegram->notifyNewRequest($kaprodi, [
                            'peminjam_nama' => $peminjaman->user->name,
                            'peminjam_role' => 'dosen',
                            'alat' => $alat->nama,
                            'jumlah' => $peminjaman->jumlah,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }
            } else {
                // Decrement stock upon approval
                $alat->stok_tersedia -= $peminjaman->jumlah;
                $alat->save();

                $updateData['status'] = 'dipinjam';
                $peminjaman->update($updateData);

                $telegram->notifyPeminjamanApproved($peminjaman->user, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $peminjaman->alat->nama,
                    'jumlah' => $peminjaman->jumlah,
                    'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                    'approver_role' => 'Kepala Lab',
                ]);

                $approvedCount++;
            }
        }

        $message = "$approvedCount Peminjaman berhasil disetujui secara final. Status: Dipinjam.";
        if (!empty($failedMessages)) {
            $message .= ' Gagal: ' . implode(', ', $failedMessages) . '.';
        }

        return redirect()->back()->with($failedMessages ? 'error' : 'success', $message);
    }

}
