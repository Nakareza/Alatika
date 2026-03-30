<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramLinkCode extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Check if the code has expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Get the user that owns this link code
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to find valid (non-expired) codes
     */
    public function scopeValid($query)
    {
        return $query->where('expires_at', '>', now());
    }
}
