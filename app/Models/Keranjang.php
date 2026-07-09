<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $fillable = [
        'user_id',
        'alat_id',
        'cartable_type',
        'cartable_id',
        'jumlah',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Automatically set cartable_type and cartable_id if empty but alat_id is filled
            if (empty($model->cartable_type) && !empty($model->alat_id)) {
                $model->cartable_type = 'App\Models\Alat';
                $model->cartable_id = $model->alat_id;
            }
            // Automatically sync back to alat_id if cartable is Alat
            if ($model->cartable_type === 'App\Models\Alat') {
                $model->alat_id = $model->cartable_id;
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cartable()
    {
        return $this->morphTo();
    }

    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }

    public function getAlatAttribute()
    {
        if ($this->cartable_type === 'App\Models\Alat') {
            return $this->cartable;
        }
        return $this->getRelationValue('alat');
    }
}
