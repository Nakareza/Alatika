<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    protected $table = 'inventaris';

    protected $fillable = [
        'nama_alat',
        'kategori',
        'kode_barang',
        'jumlah_stok',
        'lokasi_simpan',
        'tahun_perolehan',
        'kondisi',
        'perlengkapan_detail',
        'is_borrowable',
    ];

    protected $casts = [
        'perlengkapan_detail' => 'array',
        'is_borrowable' => 'boolean',
        'tahun_perolehan' => 'integer',
        'jumlah_stok' => 'integer',
    ];
}
