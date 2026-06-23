<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Peminjaman;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'nim',
        'nip',
        'password',
        'role',
        'telegram_chat_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has Telegram linked
     */
    public function hasTelegram(): bool
    {
        return !empty($this->telegram_chat_id);
    }

    /**
     * Get telegram link codes for this user
     */
    public function telegramLinkCodes()
    {
        return $this->hasMany(TelegramLinkCode::class);
    }

    /**
     * Get all peminjaman for this user
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(Peminjaman::class);
    }
    /**
     * Get peminjaman that this user approved
     */
    public function approvedPeminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'approved_by');
    }

    /**
     * Get active peminjaman count
     */
    public function activePeminjamanCount(): int
    {
        return $this->peminjaman()->whereIn('status', ['dipinjam', 'disetujui'])->count();
    }

    /**
     * Check if the user is still using the default password (NIM/NIP without dots).
     */
    public function isUsingDefaultPassword(): bool
    {
        $identifier = $this->nim ?: $this->nip;
        if (!$identifier) {
            return false;
        }
        $defaultPassword = str_replace('.', '', $identifier);
        return \Illuminate\Support\Facades\Hash::check($defaultPassword, $this->password);
    }
}
