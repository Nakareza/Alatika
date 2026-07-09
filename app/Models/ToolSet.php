<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolSet extends Model
{
    protected $table = 'tool_sets';

    protected $fillable = [
        'kode_tool_set',
        'nama_tool_set',
        'kategori_id',
        'stok',
        'stok_tersedia',
        'lokasi',
        'kondisi',
        'keterangan',
        'tahun',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function details()
    {
        return $this->hasMany(ToolSetDetail::class, 'tool_set_id');
    }

    public function peminjaman()
    {
        return $this->morphMany(Peminjaman::class, 'borrowable');
    }

    public function isAvailable(int $jumlah = 1): bool
    {
        return $this->stok_tersedia >= $jumlah;
    }
}
