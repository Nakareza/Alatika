<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolSetDetail extends Model
{
    protected $table = 'tool_set_details';

    protected $fillable = [
        'tool_set_id',
        'nama_komponen',
        'jumlah',
        'satuan',
        'keterangan',
    ];

    public function toolSet()
    {
        return $this->belongsTo(ToolSet::class, 'tool_set_id');
    }
}
