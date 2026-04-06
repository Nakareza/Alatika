<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    protected $table = 'alat';

    protected $fillable = [
        'nama',
        'kode',
        'kategori',
        'stok_total',
        'stok_tersedia',
        'lokasi',
        'deskripsi',
        'status',
    ];

    /**
     * Get all peminjaman for this alat
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function waitlists()
    {
        return $this->hasMany(\App\Models\Waitlist::class);
    }

    /**
     * Get active (dipinjam) count
     */
    public function getStokDipinjamAttribute(): int
    {
        return $this->stok_total - $this->stok_tersedia;
    }

    /**
     * Check if alat is available
     */
    public function isAvailable(int $jumlah = 1): bool
    {
        return $this->status === 'tersedia' && $this->stok_tersedia >= $jumlah;
    }
}
