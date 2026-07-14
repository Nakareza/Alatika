<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\Alat;
use App\Models\ToolSet;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        // Admin menangani peminjaman yang membutuhkan persetujuan admin
        $query = Peminjaman::with(['user', 'borrowable'])
            ->whereJsonContains('required_approvals', 'admin');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($user) use ($search) {
                    $user->where('name', 'like', "%{$search}%");
                })
                ->orWhere(function ($sub) use ($search) {
                    $sub->where(function ($q1) use ($search) {
                        $q1->where('borrowable_type', Alat::class)
                           ->whereHasMorph('borrowable', [Alat::class], function ($q) use ($search) {
                               $q->where('nama', 'like', "%{$search}%");
                           });
                    })->orWhere(function ($q2) use ($search) {
                        $q2->where('borrowable_type', ToolSet::class)
                           ->whereHasMorph('borrowable', [ToolSet::class], function ($q) use ($search) {
                               $q->where('nama_tool_set', 'like', "%{$search}%");
                           });
                    });
                })
                ->orWhere('kode_peminjaman', 'like', "%{$search}%");
            });
        }

        $peminjaman = $query->orderBy('created_at', 'desc')->paginate(10);

        // Stats untuk peminjaman yang membutuhkan persetujuan admin
        $mhs = Peminjaman::whereJsonContains('required_approvals', 'admin');
        $stats = [
            'total'   => (clone $mhs)->count(),
            'pending' => (clone $mhs)->where('status', 'pending')->count(),
            'aktif'   => (clone $mhs)->where('status', 'dipinjam')->count(),
            'ditolak' => (clone $mhs)->where('status', 'ditolak')->count(),
        ];

        $keperluanOptions = $this->getKeperluanOptions();

        return view('admin.peminjaman.index', compact('peminjaman', 'stats', 'keperluanOptions'));
    }

    public function approve($id, TelegramService $telegram)
    {
        $peminjaman = Peminjaman::with(['user', 'borrowable'])->findOrFail($id);

        $isManualExternalLoan = $peminjaman->user_id === null && filled($peminjaman->nama_peminjam_non_user);

        if ($peminjaman->required_approvals !== null) {
            if (!in_array('admin', $peminjaman->required_approvals)) {
                return redirect()->back()->with('error', 'Akses ditolak. Peminjaman ini tidak memerlukan persetujuan Admin.');
            }
        } else {
            if (!$isManualExternalLoan && $peminjaman->user->role !== 'mahasiswa') {
                return redirect()->back()->with('error', 'Admin hanya dapat menyetujui peminjaman mahasiswa.');
            }
        }

        $borrowable = $peminjaman->borrowable;
        if (!$borrowable) {
            return redirect()->back()->with('error', 'Data inventaris tidak ditemukan.');
        }

        $itemName = $peminjaman->borrowable_type === ToolSet::class ? $borrowable->nama_tool_set : $borrowable->nama;

        // Check stock availability before approving
        if ($borrowable->stok_tersedia < $peminjaman->jumlah) {
            return redirect()->back()->with('error', 'Stok "' . $itemName . '" tidak mencukupi (tersedia: ' . $borrowable->stok_tersedia . ', diminta: ' . $peminjaman->jumlah . ').');
        }

        $updateData = [
            'admin_approved_by' => Auth::id(),
            'admin_approved_at' => now(),
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
                $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);
                $peminjaman->update(['status' => 'dipinjam']);

                if ($peminjaman->user) {
                    $telegram->notifyPeminjamanApproved($peminjaman->user, [
                        'kode' => $peminjaman->kode_peminjaman,
                        'alat' => $itemName,
                        'jumlah' => $peminjaman->jumlah,
                        'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                        'approver_role' => $approverList,
                    ]);
                }

                return redirect()->back()->with('success', "Peminjaman disetujui secara final (Disetujui oleh {$approverList}). Status: Dipinjam.");
            } else {
                // Find who is next to approve
                $pendingApprovers = [];
                if (in_array('admin', $peminjaman->required_approvals) && $peminjaman->admin_approved_by === null) {
                    $pendingApprovers[] = 'Admin';
                }
                if (in_array('kalab', $peminjaman->required_approvals) && $peminjaman->kalab_approved_by === null) {
                    $pendingApprovers[] = 'Kepala Lab';
                    // Notify Kalab
                    $kalabs = User::where('role', 'kalab')->whereNotNull('telegram_chat_id')->get();
                    foreach ($kalabs as $kalab) {
                        $telegram->notifyNewRequest($kalab, [
                            'peminjam_nama' => $peminjaman->user->name,
                            'peminjam_role' => $peminjaman->user->role,
                            'alat' => $itemName,
                            'jumlah' => $peminjaman->jumlah,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }
                if (in_array('kaprodi', $peminjaman->required_approvals) && $peminjaman->kaprodi_approved_by === null) {
                    $pendingApprovers[] = 'Kaprodi';
                    // Notify Kaprodi
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
                            'peminjam_nama' => $peminjaman->user?->name ?? $peminjaman->nama_peminjam_non_user ?? 'Peminjam',
                            'peminjam_role' => $peminjaman->user ? $peminjaman->user->role : 'Organisasi/Luar',
                            'alat' => $itemName,
                            'jumlah' => $peminjaman->jumlah,
                            'kode' => $peminjaman->kode_peminjaman,
                        ]);
                    }
                }

                $pendingList = implode(', ', $pendingApprovers);
                return redirect()->back()->with('success', "Peminjaman disetujui oleh Admin. Menunggu persetujuan dari: {$pendingList}.");
            }
        }

        // Double approval for student special tools (program_studi !== null)
        $isSpecialTool = ($peminjaman->borrowable_type === Alat::class) && ($borrowable->program_studi !== null);

        if ($isSpecialTool) {
            $peminjaman->update($updateData);

            if ($peminjaman->kalab_approved_by !== null) {
                // Decrement stock upon final approval
                $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);
                $peminjaman->update(['status' => 'dipinjam']);

                $telegram->notifyPeminjamanApproved($peminjaman->user, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $itemName,
                    'jumlah' => $peminjaman->jumlah,
                    'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                    'approver_role' => 'Admin dan Kepala Lab',
                ]);

                return redirect()->back()->with('success', 'Peminjaman Mahasiswa disetujui. Status: Dipinjam (Disetujui oleh Admin & Kepala Lab).');
            } else {
                // Notify Kalab
                $kalabs = User::where('role', 'kalab')->whereNotNull('telegram_chat_id')->get();
                foreach ($kalabs as $kalab) {
                    $telegram->notifyNewRequest($kalab, [
                        'peminjam_nama' => $peminjaman->user->name,
                        'peminjam_role' => 'mahasiswa',
                        'alat' => $itemName . ' (Alat Khusus - Butuh Kalab)',
                        'jumlah' => $peminjaman->jumlah,
                        'kode' => $peminjaman->kode_peminjaman,
                    ]);
                }
                return redirect()->back()->with('success', 'Peminjaman disetujui oleh Admin. Menunggu persetujuan Kepala Lab.');
            }
        }

        // Standard single approval (decrements stock immediately)
        $borrowable->decrement('stok_tersedia', $peminjaman->jumlah);

        $updateData['status'] = 'dipinjam';
        $peminjaman->update($updateData);

if ($peminjaman->user) {
                $telegram->notifyPeminjamanApproved($peminjaman->user, [
                    'kode' => $peminjaman->kode_peminjaman,
                    'alat' => $itemName,
                    'jumlah' => $peminjaman->jumlah,
                    'deadline' => $peminjaman->tanggal_kembali->format('d M Y'),
                    'approver_role' => 'Admin',
                ]);
            }

        return redirect()->back()->with('success', 'Peminjaman Mahasiswa disetujui. Status: Dipinjam.');
    }

    public function reject(Request $request, $id, TelegramService $telegram)
    {
        $request->validate(['alasan' => 'required|string']);

        $peminjaman = Peminjaman::with(['user', 'borrowable'])->findOrFail($id);

        $isManualExternalLoan = $peminjaman->user_id === null && filled($peminjaman->nama_peminjam_non_user);

        // Guard: admin hanya reject mahasiswa, kecuali peminjaman manual luar user yang memang butuh approval admin.
        if (!$isManualExternalLoan && ($peminjaman->user === null || $peminjaman->user->role !== 'mahasiswa')) {
            return redirect()->back()->with('error', 'Admin hanya dapat menolak peminjaman mahasiswa.');
        }

        $borrowable = $peminjaman->borrowable;
        $itemName = $peminjaman->borrowable_type === ToolSet::class ? $borrowable->nama_tool_set : $borrowable->nama;

        $peminjaman->update([
            'status' => 'ditolak',
            'rejected_reason' => $request->alasan,
            'admin_approved_by' => Auth::id(),
            'admin_approved_at' => now(),
        ]);

        if ($peminjaman->user) {
            $telegram->notifyPeminjamanRejected($peminjaman->user, [
                'kode' => $peminjaman->kode_peminjaman,
                'alat' => $itemName,
                'alasan' => $request->alasan,
            ]);
        }

        return redirect()->back()->with('success', 'Peminjaman Mahasiswa ditolak.');
    }

    public function markAsBorrowed($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if ($peminjaman->status !== 'dipinjam') {
            return redirect()->back()->with('error', 'Peminjaman harus disetujui dahulu.');
        }

        return redirect()->back()->with('success', 'Status peminjaman sudah DIPINJAM.');
    }

    public function approveReturn($id)
    {
        $peminjaman = Peminjaman::with('borrowable')->findOrFail($id);

        if ($peminjaman->status !== 'menunggu_verifikasi') {
            return back()->with('error', 'Status tidak valid');
        }

        // update status
        $peminjaman->update([
            'status' => 'selesai',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // kembalikan stok
        $borrowable = $peminjaman->borrowable;
        if ($borrowable) {
            $borrowable->increment('stok_tersedia', $peminjaman->jumlah);
        }

        return back()->with('success', 'Pengembalian berhasil diverifikasi');
    }

    public function rejectReturn($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        $peminjaman->update([
            'status' => 'dipinjam',
        ]);

        return redirect()->back()->with('success', 'Pengembalian ditolak.');
    }

    // ===================================================
    // KELOLA KEPERLUAN
    // ===================================================

    public function addKeperluan(Request $request)
    {
        $request->validate([
            'keperluan' => 'required|string|max:100',
        ]);

        $options = static::getKeperluanOptions();
        $newOption = trim($request->keperluan);
        $sameDay = $request->boolean('same_day');

        // Prevent duplicates
        if (in_array($newOption, array_column($options, 'name'))) {
            return redirect()->back()->with('error', 'Keperluan "' . $newOption . '" sudah ada.');
        }

        $options[] = ['name' => $newOption, 'same_day' => $sameDay];
        static::saveKeperluanOptions($options);

        $label = $sameDay ? ' (wajib kembali 1 hari)' : '';
        return redirect()->back()->with('success', 'Keperluan "' . $newOption . '"' . $label . ' berhasil ditambahkan.');
    }

    public function removeKeperluan(Request $request)
    {
        $request->validate([
            'keperluan' => 'required|string',
        ]);

        $options = static::getKeperluanOptions();
        $options = array_values(array_filter($options, fn($o) => $o['name'] !== $request->keperluan));
        static::saveKeperluanOptions($options);

        return redirect()->back()->with('success', 'Keperluan berhasil dihapus.');
    }
}
