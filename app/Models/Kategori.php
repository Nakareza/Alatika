<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategoris';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    public function alats()
    {
        return $this->hasMany(Alat::class, 'kategori_id');
    }

    public function toolSets()
    {
        return $this->hasMany(ToolSet::class, 'kategori_id');
    }
}
