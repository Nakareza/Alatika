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
        'alat_id',
        'jumlah',
        'keperluan',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'approved_by',
        'approved_at',
        'rejected_reason',
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
        'tanggal_dikembalikan' => 'datetime',

        'reminder_h1_sent'   => 'boolean',
        'reminder_hday_sent' => 'boolean',
        'overdue_d1_sent'    => 'boolean',
        'overdue_d3_sent'    => 'boolean',
        'overdue_d7_sent'    => 'boolean',
    ];

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
     * Alat yang dipinjam
     */
    public function alat()
    {
        return $this->belongsTo(Alat::class);
    }

    /**
     * User yang menyetujui
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
                'icon'  => 'fa-clock',
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
}