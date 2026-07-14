<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $fillable = [
        'kode_peminjaman',
        'user_id',
        'nama_peminjam_non_user',
        'alat_id',
        'borrowable_type',
        'borrowable_id',
        'jumlah',
        'keperluan',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'approved_by',
        'approved_at',
        'kalab_approved_by',
        'kalab_approved_at',
        'admin_approved_by',
        'admin_approved_at',
        'kaprodi_approved_by',
        'kaprodi_approved_at',
        'rejected_reason',
        'surat_keterangan',
        'required_approvals',
        'foto_bukti_kembali',
        'telegram_photo_file_id',
        'tanggal_dikembalikan',
        'catatan_kondisi',
        'kondisi_kembali',
        'reminder_h1_sent',
        'reminder_hday_sent',
        'overdue_d1_sent',
        'overdue_d3_sent',
        'overdue_d7_sent',
    ];

    /**
     * Cast attribute otomatis
     * jadi tanggal langsung jadi object Carbon
     */
    protected $casts = [
        'tanggal_pinjam'       => 'date',
        'tanggal_kembali'      => 'date',
        'approved_at'          => 'datetime',
        'kalab_approved_at'    => 'datetime',
        'admin_approved_at'    => 'datetime',
        'kaprodi_approved_at'  => 'datetime',
        'tanggal_dikembalikan' => 'datetime',
        'required_approvals'   => 'array',

        'reminder_h1_sent'   => 'boolean',
        'reminder_hday_sent' => 'boolean',
        'overdue_d1_sent'    => 'boolean',
        'overdue_d3_sent'    => 'boolean',
        'overdue_d7_sent'    => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Automatically set borrowable_type and borrowable_id if empty but alat_id is filled
            if (empty($model->borrowable_type) && !empty($model->alat_id)) {
                $model->borrowable_type = 'App\Models\Alat';
                $model->borrowable_id = $model->alat_id;
            }
            // Automatically sync back to alat_id if borrowable is Alat
            if ($model->borrowable_type === 'App\Models\Alat') {
                $model->alat_id = $model->borrowable_id;
            }
        });
    }

    // ===================================================
    // RELATIONSHIPS
    // ===================================================

    /**
     * User peminjam
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relation
     */
    public function borrowable()
    {
        return $this->morphTo();
    }

    /**
     * Internal relation helper for Alat
     */
    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    /**
     * Accessor to fallback to borrowable when it is an Alat
     */
    public function getAlatAttribute()
    {
        if ($this->borrowable_type === 'App\Models\Alat') {
            return $this->borrowable;
        }
        return $this->getRelationValue('alat');
    }

    /**
     * User yang menyetujui
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Kepala Lab yang menyetujui
     */
    public function kalabApprover()
    {
        return $this->belongsTo(User::class, 'kalab_approved_by');
    }

    /**
     * Admin yang menyetujui
     */
    public function adminApprover()
    {
        return $this->belongsTo(User::class, 'admin_approved_by');
    }

    /**
     * Kaprodi yang menyetujui
     */
    public function kaprodiApprover()
    {
        return $this->belongsTo(User::class, 'kaprodi_approved_by');
    }

    // ===================================================
    // SCOPES
    // ===================================================

    /**
     * Status pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Peminjaman aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'dipinjam');
    }

    /**
     * Sedang dipinjam
     */
    public function scopeDipinjam($query)
    {
        return $query->where('status', 'dipinjam');
    }

    /**
     * Menunggu verifikasi pengembalian
     */
    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status', 'menunggu_verifikasi');
    }

    /**
     * Selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status', 'selesai');
    }

    /**
     * Ditolak
     */
    public function scopeDitolak($query)
    {
        return $query->where('status', 'ditolak');
    }

    /**
     * Sudah lewat deadline
     */
    public function scopeOverdue($query)
    {
        return $query
            ->where('status', 'dipinjam')
            ->where('tanggal_kembali', '<', now()->startOfDay());
    }

    /**
     * Deadline mendekati
     */
    public function scopeDeadlineSoon($query, int $days = 1)
    {
        return $query
            ->where('status', 'dipinjam')
            ->whereBetween('tanggal_kembali', [
                now()->startOfDay(),
                now()->addDays($days)->endOfDay(),
            ]);
    }

    /**
     * Berdasarkan user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ===================================================
    // HELPER METHODS
    // ===================================================

    /**
     * Generate kode unik
     * contoh: PMJ-ABCD
     */
    public static function generateKode(): string
    {
        do {
            $kode = 'PMJ-' . strtoupper(Str::random(4));
        } while (
            self::where('kode_peminjaman', $kode)->exists()
        );

        return $kode;
    }

    /**
     * Apakah terlambat
     */
    public function isOverdue(): bool
    {
        return $this->status === 'dipinjam'
            && $this->tanggal_kembali->isPast();
    }

    /**
     * Jumlah hari keterlambatan
     */
    public function getDaysOverdueAttribute(): int
    {
        return (int) now()
            ->startOfDay()
            ->diffInDays(
                $this->tanggal_kembali,
                false
            ) * -1;
    }

    /**
     * Label status untuk UI
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {

            'pending' =>
                'Menunggu Persetujuan',

            'dipinjam' =>
                'Sedang Dipinjam',

            'menunggu_verifikasi' =>
                'Menunggu Verifikasi',

            'selesai' =>
                'Selesai',

            'ditolak' =>
                'Ditolak',

            default =>
                ucfirst($this->status),
        };
    }

    /**
     * Badge status untuk UI
     */
    public function getStatusConfigAttribute(): array
    {
        return match ($this->status) {

            'pending' => [
                'color' => 'bg-amber-100 text-amber-700',
                'icon'  => 'fa-hourglass-start',
            ],

            'dipinjam' => [
                'color' => 'bg-indigo-100 text-indigo-700',
                'icon'  => 'fa-hand-holding',
            ],

            'menunggu_verifikasi' => [
                'color' => 'bg-purple-100 text-purple-700',
                'icon'  => 'fa-camera',
            ],

            'selesai' => [
                'color' => 'bg-emerald-100 text-emerald-700',
                'icon'  => 'fa-check-double',
            ],

            'ditolak' => [
                'color' => 'bg-red-100 text-red-700',
                'icon'  => 'fa-times',
            ],

            default => [
                'color' => 'bg-gray-100 text-gray-700',
                'icon'  => 'fa-question',
            ],
        };
    }

    /**
     * URL foto bukti pengembalian
     */
    public function getFotoBuktiUrlAttribute(): ?string
    {
        if (!$this->foto_bukti_kembali) {
            return null;
        }

        return asset(
            'storage/' . $this->foto_bukti_kembali
        );
    }

    /**
     * Get the display name of the borrower (registered user or custom non-user name)
     */
    public function getNamaPeminjamAttribute(): string
    {
        return $this->user ? $this->user->name : ($this->nama_peminjam_non_user ?: '-');
    }

    /**
     * Get the role description of the borrower
     */
    public function getPeminjamRoleAttribute(): string
    {
        return $this->user ? ucfirst($this->user->role) : 'Organisasi/Luar';
    }

    /**
     * Get approval status information for display
     * Returns array with approval info for admin/kalab actions
     */
    public function getApprovalStatusAttribute(): array
    {
        $status = [];

        // Check admin approval
        if ($this->admin_approved_by !== null) {
            $status['admin_approved'] = true;
            $status['admin_approver'] = $this->adminApprover?->name ?? 'Admin';
            $status['admin_approved_at'] = $this->admin_approved_at;
        } else {
            $status['admin_approved'] = false;
        }

        // Check kalab approval
        if ($this->kalab_approved_by !== null) {
            $status['kalab_approved'] = true;
            $status['kalab_approver'] = $this->kalabApprover?->name ?? 'Kepala Lab';
            $status['kalab_approved_at'] = $this->kalab_approved_at;
        } else {
            $status['kalab_approved'] = false;
        }

        // Check kaprodi approval
        if ($this->kaprodi_approved_by !== null) {
            $status['kaprodi_approved'] = true;
            $status['kaprodi_approver'] = $this->kaprodiApprover?->name ?? 'Kaprodi';
            $status['kaprodi_approved_at'] = $this->kaprodi_approved_at;
        } else {
            $status['kaprodi_approved'] = false;
        }

        return $status;
    }

    /**
     * Get formatted approval status message
     */
    public function getApprovalStatusMessageAttribute(): string
    {
        if ($this->status === 'ditolak') {
            return 'Ditolak';
        }

        if ($this->status === 'pending') {
            return 'Menunggu Persetujuan';
        }

        $approvalStatus = $this->approval_status;

        if ($this->status === 'dipinjam') {
            $approved_by = [];

            if ($approvalStatus['admin_approved']) {
                $approved_by[] = 'Admin';
            }
            if ($approvalStatus['kalab_approved']) {
                $approved_by[] = 'Kalab';
            }
            if ($approvalStatus['kaprodi_approved']) {
                $approved_by[] = 'Kaprodi';
            }

            if (!empty($approved_by)) {
                return 'Disetujui oleh: ' . implode(', ', $approved_by);
            }
        }

        return $this->status_label;
    }

    /**
     * Get name of the borrowed item (Alat or ToolSet)
     */
    public function getItemNameAttribute(): string
    {
        if ($this->borrowable_type === 'App\Models\ToolSet') {
            return $this->borrowable?->nama_tool_set ?? 'Tool Set';
        }
        return $this->alat?->nama ?? '-';
    }

    /**
     * Check if waiting for kalab approval
     */
    public function isWaitingForKalabApproval(): bool
    {
        // Check if this is a special tool (program_studi !== null for Alat)
        $isSpecialTool = ($this->borrowable_type === Alat::class) &&
                         ($this->borrowable?->program_studi !== null);

        if ($isSpecialTool && $this->status === 'pending' && $this->kalab_approved_by === null) {
            return true;
        }

        // Check if required_approvals includes kalab
        if ($this->required_approvals && in_array('kalab', $this->required_approvals) && $this->kalab_approved_by === null) {
            return true;
        }

        return false;
    }

    /**
     * Get next approver role that needs to approve
     */
    public function getNextApproverRoleAttribute(): ?string
    {
        if ($this->status !== 'pending') {
            return null;
        }

        // Check for required approvals
        if ($this->required_approvals && is_array($this->required_approvals)) {
            $required = $this->required_approvals;

            if (in_array('admin', $required) && $this->admin_approved_by === null) {
                return 'Admin';
            }
            if (in_array('kalab', $required) && $this->kalab_approved_by === null) {
                return 'Kepala Lab';
            }
            if (in_array('kaprodi', $required) && $this->kaprodi_approved_by === null) {
                return 'Kaprodi';
            }
        } else {
            // Check if special tool (needs kalab and admin/kaprodi approval)
            $isSpecialTool = ($this->borrowable_type === Alat::class) &&
                             ($this->borrowable?->program_studi !== null);

            if ($isSpecialTool) {
                if ($this->user?->role === 'dosen') {
                    if ($this->kalab_approved_by === null) {
                        return 'Kepala Lab';
                    }
                    if ($this->kaprodi_approved_by === null) {
                        return 'Kaprodi';
                    }
                } else { // Mahasiswa
                    if ($this->admin_approved_by === null) {
                        return 'Admin';
                    }
                    if ($this->kalab_approved_by === null) {
                        return 'Kepala Lab';
                    }
                }
            }
        }

        return null;
    }
}