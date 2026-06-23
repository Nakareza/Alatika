<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswords extends Command
{
    protected $signature = 'users:reset-passwords';
    protected $description = 'Reset all user passwords to their NIM/NIP without dots';

    public function handle(): int
    {
        $users = User::whereNotNull('nim')->orWhereNotNull('nip')->get();

        $count = 0;
        foreach ($users as $user) {
            $identifier = $user->nim ?? $user->nip;
            // Remove dots from NIM (e.g., "4.33.25.0.01" → "43325001")
            // NIP has no dots, but the replace is harmless
            $password = str_replace('.', '', $identifier);

            $user->password = Hash::make($password);
            $user->save();
            $count++;

            $this->line("  OK: {$user->name} → password: {$password}");
        }

        $this->info('');
        $this->info("Total passwords reset: {$count}");
        return self::SUCCESS;
    }
}
