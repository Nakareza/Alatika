<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed default users for each role.
     */
    public function run(): void
    {
        // Admin (Teknisi)
        User::updateOrCreate(
            ['email' => 'admin@alatika.com'],
            [
                'name'     => 'Admin Teknisi',
                'nip'      => '198501012010011001',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // Kepala Laboratorium
        User::updateOrCreate(
            ['email' => 'kalab@alatika.com'],
            [
                'name'     => 'Dr. Budi Hartono',
                'nip'      => '197803152005011002',
                'password' => Hash::make('password'),
                'role'     => 'kalab',
            ]
        );

        // Dosen
        User::updateOrCreate(
            ['email' => 'dosen@alatika.com'],
            [
                'name'     => 'Ir. Siti Aminah, M.T.',
                'nip'      => '198205202008012003',
                'password' => Hash::make('password'),
                'role'     => 'dosen',
            ]
        );

        // Mahasiswa
        User::updateOrCreate(
            ['email' => 'mahasiswa@alatika.com'],
            [
                'name'     => 'Ahmad Rizki Saputra',
                'nim'      => '23010001',
                'password' => Hash::make('password'),
                'role'     => 'mahasiswa',
            ]
        );
    }
}
