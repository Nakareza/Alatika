<?php

namespace App\Http\Controllers\Kalab;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\ToolSet;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeminjamanController extends Controller
{
    private function isManualExternalLoan(Peminjaman $peminjaman): bool
    {
        return $peminjaman->user_id === null && filled($peminjaman->nama_peminjam_non_user);
    }

    private function notifyApprovedToBorrower(TelegramService $telegram, Peminjaman $peminjaman, array $data): void
    {
        if ($peminjaman->user) {
            $telegram->notifyPeminjamanApproved($peminjaman->user, $data);
        }
    }

    private function notifyRejectedToBorrower(TelegramService $telegram, Peminjaman $peminjaman, array $data): void
    {
        if ($peminjaman->user) {
            $telegram->notifyPeminjamanRejected($peminjaman->user, $data);
        }
    }

    public function persetujuan(Request $request)
    {
        $group = $request->get('group', 'all');

        $query = Peminjaman::with(['user', 'alat'])
            ->whereJsonContains('required_approvals', 'kalab');

        if ($group === 'dosen') {
            $query->whereHas('user', function ($u) {
                $u->where('role', 'dosen');
            });
        } elseif ($group === 'mahasiswa') {
            $query->whereHas('user', function ($u) {
                $u->where('role', 'mahasiswa');
            });
        } elseif ($group === 'organisasi') {
            $query->whereNull('user_id')
                ->whereNotNull('nama_peminjam_non_user');
        }

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
        $statsQuery = Peminjaman::whereJsonContains('required_approvals', 'kalab');

        if ($group === 'dosen') {
            $statsQuery->whereHas('user', function ($u) {
                $u->where('role', 'dosen');
            });
        } elseif ($group === 'mahasiswa') {
            $statsQuery->whereHas('user', function ($u) {
                $u->where('role', 'mahasiswa');
            });
        } elseif ($group === 'organisasi') {
            $statsQuery->whereNull('user_id')
                ->whereNotNull('nama_peminjam_non_user');
        }

        $stats = [
            'pending' => (clone $statsQuery)->where('status', 'pending')->count(),
            'dipinjam' => (clone $statsQuery)->where('status', 'dipinjam')->count(),
            'selesai' => (clone $statsQuery)->where('status', 'selesai')->count(),
            'total_pengajuan' => (clone $statsQuery)->count(),
        ];

        return view(
            'kalab.persetujuan.index',
            compact('peminjaman', 'stats', 'group')
        );
    }

    public function riwayat(Request $request)
    {
        // Ka Lab melihat riwayat peminjaman yang memerlukan persetujuan Kepala Lab
        $peminjaman = Peminjaman::with(['user', 'alat'])
            ->whereJsonContains('required_approvals', 'kalab')
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
        $peminjaman = Peminjaman::with(['user', 'borrowable'])->findOrFail($id);

        $borrowable = $peminjaman->borrowable;
        if (!$borrowable) {
            return redirect()->back()->with('error', 'Data inventaris tidak ditemukan.');
        }

        $isSpecialTool = ($peminjaman->borrowable_type === \App\Models\Alat::class) && ($borrowable->program_studi !== null);
        $borrowerRole = $peminjaman->user?->role;
        $isManualExternalLoan = $this->isManualExternalLoan($peminjaman);
        $borrowerName = $peminjaman->user?->name ?? $peminjaman->nama_peminjam_non_user ?? 'Peminjam';
        $borrowerRoleLabel = $peminjaman->user ? $peminjaman->peminjam_role : 'Organisasi/Luar';

        // Guard: kalab hanya approve dosen ATAU mahasiswa dengan alat khusus.
        if ($peminjaman->required_approvals !== null) {
            if (!in_array('kalab', $peminjaman->required_approvals)) {
                return redirect()->back()->with('error', 'Akses ditolak. Peminjaman ini tidak memerlukan persetujuan Kepala Lab.');
            }
        } else {
            if (!$isManualExternalLoan && $borrowerRole !== 'dosen' && !($borrowerRole === 'mahasiswa' && $isSpecialTool)) {
                return redirect()->back()->with('error', 'Akses ditolak. Anda tidak berwenang menyetujui peminjaman ini.');
            }
        }

        $itemName = $peminjaman->borrowable_type === \App\Models\ToolSet::class ? $borrowable->nama_tool_set : $borrowable->nama;

        // Check stock availability before approving
        if ($borrowable->stok_tersedia < $peminjaman->jumlah) {
            return redirect()->back()->with('error', 'Stok "' . $itemName . '" tidak mencukupi (tersedia: ' . $borrowable->stok_tersedia . ', diminta: ' . $peminjaman->jumlah . ').');
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
                $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);
                $peminjaman->update(['status' => 'dipinjam']);

                $this->notifyApprovedToBorrower($telegram, $peminjaman, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $itemName,
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
                    // Notify Admin/Teknisi
                    $admins = User::where('role', 'admin')->whereNotNull('telegram_chat_id')->get();
                    foreach ($admins as $admin) {
                        $telegram->notifyNewRequest($admin, [
                            'peminjam_nama' => $borrowerName,
                            'peminjam_role' => $borrowerRoleLabel,
                            'alat' => $itemName,
                            'jumlah' => $peminjaman->jumlah,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }
                if (in_array('kalab', $peminjaman->required_approvals) && $peminjaman->kalab_approved_by === null) {
                    $pendingApprovers[] = 'Kepala Lab';
                }
                if (in_array('kaprodi', $peminjaman->required_approvals) && $peminjaman->kaprodi_approved_by === null) {
                    $pendingApprovers[] = 'Kaprodi';
                    // Notify Kaprodi (filtered by prodi)
                    $borrowerProdi = $peminjaman->user?->program_studi;
                    $kaprodis = User::where('role', 'kaprodi')
                        ->whereNotNull('telegram_chat_id')
                        ->get()
                        ->filter(function ($kaprodi) use ($borrowerProdi, $peminjaman) {
                            if ($borrowerProdi) {
                                $borrowerShort = str_contains($borrowerProdi, 'D3') ? 'D3' : 'D4';
                                $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                                return $borrowerShort === $kProdiShort;
                            }
                            $toolProdi = $peminjaman->alat?->program_studi;
                            if ($toolProdi) {
                                $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                                return str_contains($toolProdi, $kProdiShort);
                            }
                            return false;
                        });
                    foreach ($kaprodis as $kaprodi) {
                        $telegram->notifyNewRequest($kaprodi, [
                            'peminjam_nama' => $borrowerName,
                            'peminjam_role' => $borrowerRoleLabel,
                            'alat' => $itemName,
                            'jumlah' => $peminjaman->jumlah,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }

                $pendingList = implode(', ', $pendingApprovers);
                return redirect()->back()->with('success', "Peminjaman disetujui oleh Kepala Lab. Menunggu persetujuan dari: {$pendingList}.");
            }
        }

        // Case 1: Student borrowing a special tool (double approval: Admin + Kalab)
        if (!$isManualExternalLoan && $borrowerRole === 'mahasiswa') {
            $peminjaman->update($updateData);

            if ($peminjaman->admin_approved_by !== null) {
                // Finalize approval: decrement stock, set status to dipinjam
                $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);
                $peminjaman->update(['status' => 'dipinjam']);

                $this->notifyApprovedToBorrower($telegram, $peminjaman, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $itemName,
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
        if (!$isManualExternalLoan && $peminjaman->borrowable_type === \App\Models\Alat::class && $borrowable->program_studi !== null) {
            $peminjaman->update($updateData);

            // If kaprodi has already approved, finalize approval
            if ($peminjaman->kaprodi_approved_by !== null) {
                // Decrement stock upon approval
                $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);
                $peminjaman->update(['status' => 'dipinjam']);

                $this->notifyApprovedToBorrower($telegram, $peminjaman, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $itemName,
                    'jumlah' => $peminjaman->jumlah,
                    'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                    'approver_role' => 'Kepala Lab dan Kaprodi',
                ]);

                return redirect()->back()->with('success', 'Peminjaman Dosen disetujui. Status: Dipinjam (Disetujui oleh Kepala Lab dan Kaprodi).');
            } else {
                // Notify Kaprodi to approve (filtered by prodi)
                $borrowerProdi = $peminjaman->user?->program_studi;
                $kaprodis = \App\Models\User::where('role', 'kaprodi')
                    ->whereNotNull('telegram_chat_id')
                    ->get()
                    ->filter(function ($kaprodi) use ($borrowerProdi, $peminjaman) {
                        if ($borrowerProdi) {
                            $borrowerShort = str_contains($borrowerProdi, 'D3') ? 'D3' : 'D4';
                            $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                            return $borrowerShort === $kProdiShort;
                        }
                        $toolProdi = $peminjaman->alat?->program_studi;
                        if ($toolProdi) {
                            $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                            return str_contains($toolProdi, $kProdiShort);
                        }
                        return false;
                    });
                foreach ($kaprodis as $kaprodi) {
                    $telegram->notifyNewRequest($kaprodi, [
                        'peminjam_nama' => $borrowerName,
                        'peminjam_role' => $borrowerRoleLabel,
                        'alat' => $itemName,
                        'jumlah' => $peminjaman->jumlah,
                        'kode' => $peminjaman->kode_peminjaman,
                    ]);
                }

                return redirect()->back()->with('success', 'Peminjaman disetujui oleh Kepala Lab. Menunggu persetujuan dari Kepala Program Studi.');
            }
        }

        // Standard single approval (decrements stock immediately)
        $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);

        $updateData['status'] = 'dipinjam';
        $peminjaman->update($updateData);

        $this->notifyApprovedToBorrower($telegram, $peminjaman, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $itemName,
            'jumlah' => $peminjaman->jumlah,
            'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
            'approver_role' => 'Kepala Lab',
        ]);

        return redirect()->back()->with('success', 'Peminjaman Dosen disetujui. Status: Dipinjam.');
    }

    public function reject(Request $request, $id, TelegramService $telegram)
    {
        $request->validate(['alasan' => 'required|string']);

        $peminjaman = Peminjaman::with(['user', 'borrowable'])->findOrFail($id);

        $borrowable = $peminjaman->borrowable;
        if (!$borrowable) {
            return redirect()->back()->with('error', 'Data inventaris tidak ditemukan.');
        }

        $isSpecialTool = ($peminjaman->borrowable_type === \App\Models\Alat::class) && ($borrowable->program_studi !== null);
        $borrowerRole = $peminjaman->user?->role;
        $isManualExternalLoan = $this->isManualExternalLoan($peminjaman);

        // Guard: kalab hanya reject dosen atau mahasiswa alat khusus.
        // Peminjaman manual luar user (organisasi/UKM) tetap bisa ditolak oleh Kalab.
        if (!$isManualExternalLoan && $borrowerRole !== 'dosen' && !($borrowerRole === 'mahasiswa' && $isSpecialTool)) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda tidak berwenang menolak peminjaman ini.');
        }

        $peminjaman->update([
            'status' => 'ditolak',
            'rejected_reason' => $request->alasan,
            'kalab_approved_by' => Auth::id(),
            'kalab_approved_at' => now(),
        ]);

        $itemName = $peminjaman->borrowable_type === \App\Models\ToolSet::class ? $borrowable->nama_tool_set : $borrowable->nama;

        $this->notifyRejectedToBorrower($telegram, $peminjaman, [
            'kode' => $peminjaman->kode_peminjaman,
            'alat' => $itemName,
            'alasan' => $request->alasan,
        ]);

        $roleLabel = $peminjaman->user?->role === 'dosen' ? 'Dosen' : ($peminjaman->user?->role === 'mahasiswa' ? 'Mahasiswa' : 'Organisasi/Luar');
        return redirect()->back()->with('success', "Peminjaman {$roleLabel} ditolak.");
    }

    public function completeReturn(Request $request, $id)
    {
        $request->validate([
            'kondisi_kembali' => 'required|in:baik,rusak_ringan,rusak_berat',
            'catatan_kondisi' => 'nullable|string',
        ]);

        $peminjaman = Peminjaman::with('borrowable')->findOrFail($id);

        if (!in_array($peminjaman->status, ['dipinjam', 'menunggu_verifikasi'])) {
            return redirect()->back()->with('error', 'Peminjaman ini tidak sedang dipinjam.');
        }

        $borrowable = $peminjaman->borrowable;
        if ($borrowable) {
            if ($request->kondisi_kembali === 'baik' || $request->kondisi_kembali === 'rusak_ringan') {
                $borrowable->stok_tersedia += $peminjaman->jumlah;
            } else {
                if ($peminjaman->borrowable_type === ToolSet::class) {
                    $borrowable->stok -= $peminjaman->jumlah;
                } else {
                    $borrowable->stok_total -= $peminjaman->jumlah;
                }
            }

            $borrowable->save();
        }

        $peminjaman->update([
            'status' => 'selesai',
            'kondisi_kembali' => $request->kondisi_kembali,
            'catatan_kondisi' => $request->catatan_kondisi,
            'tanggal_dikembalikan' => now(),
        ]);

        return redirect()->back()->with('success', 'Peminjaman berhasil ditandai selesai.');
    }

    public function bulkApprove(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'peminjaman_ids' => 'required|array',
            'peminjaman_ids.*' => 'exists:peminjaman,id',
        ]);

        $ids = $request->peminjaman_ids;
        $peminjamans = Peminjaman::with(['user', 'borrowable'])
            ->whereIn('id', $ids)
            ->where('status', 'pending')
            ->where(function ($query) {
                $query->whereHas('user', fn($u) => $u->where('role', 'dosen'))
                      ->orWhere(function ($sub) {
                          $sub->whereHas('user', fn($u) => $u->where('role', 'mahasiswa'))
                              ->where('borrowable_type', \App\Models\Alat::class)
                              ->whereHasMorph('borrowable', [\App\Models\Alat::class], fn($a) => $a->whereNotNull('program_studi'))
                              ->whereNotNull('admin_approved_by');
                      });
            })
            ->get();

        $approvedCount = 0;
        $failedMessages = [];

        foreach ($peminjamans as $peminjaman) {
            $borrowable = $peminjaman->borrowable;
            if (!$borrowable) continue;

            $itemName = $peminjaman->borrowable_type === \App\Models\ToolSet::class ? $borrowable->nama_tool_set : $borrowable->nama;

            // Check stock availability
            if ($borrowable->stok_tersedia < $peminjaman->jumlah) {
                $failedMessages[] = '"' . $itemName . '" stok tidak mencukupi (tersedia: ' . $borrowable->stok_tersedia . ')';
                continue;
            }

            $updateData = [
                'kalab_approved_by' => Auth::id(),
                'kalab_approved_at' => now(),
            ];

            $isSpecialTool = ($peminjaman->borrowable_type === \App\Models\Alat::class) && ($borrowable->program_studi !== null);

            // Case 1: Student borrowing special tool (Admin + Kalab)
            if ($peminjaman->user->role === 'mahasiswa') {
                $peminjaman->update($updateData);

                if ($peminjaman->admin_approved_by !== null) {
                    $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);
                    $peminjaman->update(['status' => 'dipinjam']);

                    $telegram->notifyPeminjamanApproved($peminjaman->user, [
                        'kode' => $peminjaman->kode_peminjaman,
                        'alat' => $itemName,
                        'jumlah' => $peminjaman->jumlah,
                        'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                        'approver_role' => 'Admin dan Kepala Lab',
                    ]);

                    $approvedCount++;
                }
                continue;
            }

            // Case 2: Dosen borrowing prodi tool (Kalab + Kaprodi)
            if ($isSpecialTool) {
                $peminjaman->update($updateData);

                // If kaprodi has already approved, finalize approval
                if ($peminjaman->kaprodi_approved_by !== null) {
                    $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);
                    $peminjaman->update(['status' => 'dipinjam']);

                    $telegram->notifyPeminjamanApproved($peminjaman->user, [
                        'kode' => $peminjaman->kode_peminjaman,
                        'alat' => $itemName,
                        'jumlah' => $peminjaman->jumlah,
                        'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                        'approver_role' => 'Kepala Lab dan Kaprodi',
                    ]);

                    $approvedCount++;
                } else {
                    // Notify Kaprodi to approve (filtered by prodi)
                    $borrowerProdi = $peminjaman->user?->program_studi;
                    $kaprodis = \App\Models\User::where('role', 'kaprodi')
                        ->whereNotNull('telegram_chat_id')
                        ->get()
                        ->filter(function ($kaprodi) use ($borrowerProdi, $peminjaman) {
                            if ($borrowerProdi) {
                                $borrowerShort = str_contains($borrowerProdi, 'D3') ? 'D3' : 'D4';
                                $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                                return $borrowerShort === $kProdiShort;
                            }
                            $toolProdi = $peminjaman->alat?->program_studi;
                            if ($toolProdi) {
                                $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                                return str_contains($toolProdi, $kProdiShort);
                            }
                            return false;
                        });
                    foreach ($kaprodis as $kaprodi) {
                        $telegram->notifyNewRequest($kaprodi, [
                            'peminjam_nama' => $peminjaman->user->name,
                            'peminjam_role' => 'dosen',
                            'alat' => $itemName,
                            'jumlah' => $peminjaman->jumlah,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }
            } else {
                // Decrement stock upon approval
                $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);

                $updateData['status'] = 'dipinjam';
                $peminjaman->update($updateData);

                $telegram->notifyPeminjamanApproved($peminjaman->user, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $itemName,
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

    public function create()
    {
        $users = \App\Models\User::whereIn('role', ['dosen', 'mahasiswa'])->orderBy('name')->get();
        $alat = \App\Models\Alat::orderBy('nama')->get();
        $categories = \App\Models\Kategori::orderBy('nama_kategori')->get();
        return view('kalab.persetujuan.create', compact('users', 'alat', 'categories'));
    }

    public function storeManual(Request $request, TelegramService $telegram)
    {
        $request->validate([
            'user_id' => 'required',
            'nama_peminjam_non_user' => 'required_if:user_id,non_user|nullable|string|max:255',
            'alat_id' => 'required|exists:alat,id',
            'jumlah' => 'required|integer|min:1',
            'keperluan' => 'required|string|max:500',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'surat_keterangan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5000',
            'approvers' => 'nullable|array',
            'approvers.*' => 'string|in:admin,kaprodi',
        ]);

        if ($request->user_id === 'non_user') {
            $request->validate([
                'surat_keterangan' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5000',
            ], [
                'surat_keterangan.required' => 'Surat keterangan wajib dilampirkan untuk peminjaman luar / organisasi.',
            ]);
        }

        $a = \App\Models\Alat::findOrFail($request->alat_id);
        if ($a->stok_tersedia < $request->jumlah) {
            return back()->with('error', 'Stok alat "' . $a->nama . '" tidak mencukupi (tersedia: ' . $a->stok_tersedia . ').');
        }

        $userId = $request->user_id === 'non_user' ? null : $request->user_id;
        $namaNonUserData = $request->user_id === 'non_user' ? $request->nama_peminjam_non_user : null;

        if ($request->filled('user_id') && $request->user_id !== 'non_user') {
            $registeredUser = \App\Models\User::find($request->user_id);
            if ($registeredUser) {
                $namaNonUserData = null;
            }
        }

        $suratKeteranganPath = null;
        if ($request->hasFile('surat_keterangan')) {
            $suratKeteranganPath = $request->file('surat_keterangan')->store('surat_keterangan', 'public');
        }

        $requiredApprovals = $request->input('approvers', []);
        if ($request->user_id === 'non_user') {
            $requiredApprovals = ['admin', 'kalab'];
        }
        $status = empty($requiredApprovals) ? 'dipinjam' : 'pending';

        $peminjaman = \App\Models\Peminjaman::create([
            'kode_peminjaman' => \App\Models\Peminjaman::generateKode(),
            'user_id' => $userId,
            'nama_peminjam_non_user' => $namaNonUserData,
            'alat_id' => $request->alat_id,
            'jumlah' => $request->jumlah,
            'keperluan' => $request->keperluan,
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'status' => $status,
            'surat_keterangan' => $suratKeteranganPath,
            'kalab_approved_by' => Auth::id(), // Auto-approved by Kalab!
            'required_approvals' => empty($requiredApprovals) ? null : $requiredApprovals,
        ]);

        if (empty($requiredApprovals)) {
            $a->decrement('stok_tersedia', $request->jumlah);

            if ($peminjaman->user) {
                $telegram->notifyPeminjamanApproved($peminjaman->user, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $a->nama,
                    'jumlah' => $request->jumlah,
                    'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                    'approver_role' => 'Sistem (Tanpa Persetujuan)',
                ]);
            }
        } else {
            $peminjamNama = $peminjaman->nama_peminjam;
            $peminjamRoleDesc = $peminjaman->peminjam_role;

            if (in_array('admin', $requiredApprovals)) {
                $admins = \App\Models\User::where('role', 'admin')->whereNotNull('telegram_chat_id')->get();
                foreach ($admins as $admin) {
                    $telegram->notifyNewRequest($admin, [
                        'peminjam_nama' => $peminjamNama,
                        'peminjam_role' => $peminjamRoleDesc,
                        'alat' => $a->nama,
                        'jumlah' => $request->jumlah,
                        'kode' => $peminjaman->kode_peminjaman,
                    ]);
                }
            }
            if (in_array('kaprodi', $requiredApprovals)) {
                $borrowerProdi = $peminjaman->user?->program_studi;
                $kaprodis = \App\Models\User::where('role', 'kaprodi')
                    ->whereNotNull('telegram_chat_id')
                    ->get()
                    ->filter(function ($kaprodi) use ($borrowerProdi, $a) {
                        if ($borrowerProdi) {
                            $borrowerShort = str_contains($borrowerProdi, 'D3') ? 'D3' : 'D4';
                            $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                            return $borrowerShort === $kProdiShort;
                        }
                        if ($a->program_studi) {
                            $kProdiShort = str_contains($kaprodi->program_studi, 'D3') ? 'D3' : 'D4';
                            return str_contains($a->program_studi, $kProdiShort);
                        }
                        return false;
                    });
                foreach ($kaprodis as $kaprodi) {
                    $telegram->notifyNewRequest($kaprodi, [
                        'peminjam_nama' => $peminjamNama,
                        'peminjam_role' => $peminjamRoleDesc,
                        'alat' => $a->nama,
                        'jumlah' => $request->jumlah,
                        'kode' => $peminjaman->kode_peminjaman,
                    ]);
                }
            }
        }

        return redirect()->route('kalab.persetujuan')->with('success', 'Peminjaman manual berhasil diinput.');
    }

}
