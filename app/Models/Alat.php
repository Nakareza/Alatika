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
        'program_studi',
        'stok_total',
        'stok_tersedia',
        'stok_maintenance',
        'lokasi',
        'tahun_pengadaan',
        'deskripsi',
        'status',
        'kondisi',
        
        // New columns
        'kategori_id',
        'kode_barang',
        'nama_barang',
        'merk',
        'spesifikasi',
        'stok',
        'satuan',
        'tahun',
        'keterangan',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Sync nama_barang and nama
            if ($model->isDirty('nama') && !$model->isDirty('nama_barang')) {
                $model->nama_barang = $model->nama;
            } elseif ($model->isDirty('nama_barang') && !$model->isDirty('nama')) {
                $model->nama = $model->nama_barang;
            }

            // Sync stok and stok_total
            if ($model->isDirty('stok_total') && !$model->isDirty('stok')) {
                $model->stok = $model->stok_total;
            } elseif ($model->isDirty('stok') && !$model->isDirty('stok_total')) {
                $model->stok_total = $model->stok;
                // If stok_tersedia is not set yet (like on create), set it to the total stock
                if (!isset($model->attributes['stok_tersedia'])) {
                    $model->stok_tersedia = $model->stok;
                }
            }

            // Sync kode and kode_barang
            if ($model->isDirty('kode') && !$model->isDirty('kode_barang')) {
                $model->kode_barang = $model->kode;
            } elseif ($model->isDirty('kode_barang') && !$model->isDirty('kode')) {
                $model->kode = $model->kode_barang;
            }

            // Sync kategori and kategori_id
            if ($model->isDirty('kategori_id') && !$model->isDirty('kategori')) {
                $kategoriModel = Kategori::find($model->kategori_id);
                if ($kategoriModel) {
                    $model->kategori = $kategoriModel->nama_kategori;
                }
            }
        });
    }

    /**
     * Get all peminjaman for this alat
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'alat_id');
    }

    public function peminjamanMorph()
    {
        return $this->morphMany(Peminjaman::class, 'borrowable');
    }

    public function kategoriRef()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /**
     * Accessor for kategori to return associated Kategori's name
     */
    public function getKategoriAttribute($value)
    {
        return $this->kategoriRef ? $this->kategoriRef->nama_kategori : ($value ?: null);
    }

    /**
     * Accessor for kode to return new kode_barang if present
     */
    public function getKodeAttribute($value)
    {
        return $this->kode_barang ?: ($value ?: null);
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
        return $this->stok_total - $this->stok_tersedia - $this->stok_maintenance;
    }

    /**
     * Check if alat is available
     */
    public function isAvailable(int $jumlah = 1): bool
    {
        return $this->status === 'tersedia' && $this->stok_tersedia >= $jumlah;
    }
}
