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

        // Kepala Program Studi D3 IK
        User::updateOrCreate(
            ['nip' => '197711192008012013'],
            [
                'name'          => 'IDHAWATI HESTININGSIH, S.KOM, M.KOM.',
                'email'         => 'kaprodi.ik@alatika.com',
                'password'      => Hash::make('197711192008012013'),
                'role'          => 'kaprodi',
                'program_studi' => 'D3 IK',
            ]
        );

        // Kepala Program Studi D4 TRK
        User::updateOrCreate(
            ['nip' => '198407192019031008'],
            [
                'name'          => 'KUWAT SANTOSO, S.T., M.KOM.',
                'email'         => 'kaprodi.ti@alatika.com',
                'password'      => Hash::make('198407192019031008'),
                'role'          => 'kaprodi',
                'program_studi' => 'D4 TRK',
            ]
        );

        // Backfill all existing students in the database
        $students = User::where('role', 'mahasiswa')->get();
        foreach ($students as $student) {
            $nim = trim((string)$student->nim);
            if ($nim !== '') {
                $firstDigit = substr(ltrim($nim), 0, 1);
                if ($firstDigit === '3') {
                    $student->update(['program_studi' => 'D3 IK']);
                } elseif ($firstDigit === '4') {
                    $student->update(['program_studi' => 'D4 TRK']);
                }
            }
        }
    }
}
