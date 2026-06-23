<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ImportUsersFromCsv extends Command
{
    protected $signature = 'users:import-from-csv';
    protected $description = 'Import all dosen and mahasiswa from the 3 CSV data files';

    private int $dosenCount = 0;
    private int $mhsTiCount = 0;
    private int $mhsIkCount = 0;

    public function handle(): int
    {
        $this->info('=== Import Data Dosen ===');
        $this->importDosen(base_path('Daftar Dosen IK TI Genap 2025-2026.csv'));

        $this->info('');
        $this->info('=== Import Data Mahasiswa TI ===');
        $this->importMahasiswa(base_path('Daftar IK TI Genap 2025-2026-TI.csv'), 'TI');

        $this->info('');
        $this->info('=== Import Data Mahasiswa IK ===');
        $this->importMahasiswa(base_path('Daftar IK TI Genap 2025-2026-IK.csv'), 'IK');

        $total = $this->dosenCount + $this->mhsTiCount + $this->mhsIkCount;
        $this->info('');
        $this->info("========================================");
        $this->info("Import Selesai!");
        $this->info("Dosen     : {$this->dosenCount}");
        $this->info("Mahasiswa TI: {$this->mhsTiCount}");
        $this->info("Mahasiswa IK: {$this->mhsIkCount}");
        $this->info("Total     : {$total}");
        $this->info("========================================");

        return self::SUCCESS;
    }

    private function importDosen(string $path): void
    {
        if (!file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            $this->error("Gagal membuka file: {$path}");
            return;
        }

        // Skip header row (No,Nama,NIP,Homebase)
        fgetcsv($handle);

        $rowNum = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;

            // Need at least 3 columns: No, Nama, NIP
            if (count($row) < 3) {
                continue;
            }

            $nama = trim($row[1] ?? '');
            $nip  = trim($row[2] ?? '');

            // Skip empty rows
            if ($nama === '' || $nip === '') {
                continue;
            }

            // Clean up NIP: remove non-digit characters
            $nip = preg_replace('/[^0-9]/', '', $nip);
            if ($nip === '') {
                continue;
            }

            // Password = NIP without dots (NIP already has no dots, but str_replace is safe)
            $password = str_replace('.', '', $nip);

            // Use firstOrCreate to avoid race conditions
            $user = User::firstOrCreate(
                ['nip' => $nip],
                [
                    'name'     => $nama,
                    'email'    => null,
                    'password' => Hash::make($password),
                    'role'     => 'dosen',
                ]
            );

            if ($user->wasRecentlyCreated) {
                $this->dosenCount++;
                $this->line("  OK: {$nama} (NIP: {$nip})");
            } else {
                $this->line("  Skip (sudah ada): {$nama} (NIP: {$nip})");
            }
        }

        fclose($handle);
    }

    private function importMahasiswa(string $path, string $label): void
    {
        if (!file_exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return;
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            $this->error("Gagal membuka file: {$path}");
            return;
        }

        while (($row = fgetcsv($handle)) !== false) {
            // Detect NIM and Name from the row
            $nim  = null;
            $nama = null;

            foreach ($row as $col) {
                $val = trim((string) $col);
                if ($val === '') {
                    continue;
                }

                // NIM pattern: contains multiple dots like 3.34.25.0.01 or 4.33.25.0.01
                if (preg_match('/^\d+\.\d+\.\d+\.\d+\.\d+$/', $val)) {
                    $nim = $val;
                }
                // NIP-like numeric (for edge cases)
                elseif (preg_match('/^\d{15,20}$/', $val) && $nim === null) {
                    // This is likely a NIP, not NIM for mahasiswa - skip
                    continue;
                }
            }

            // Name is the last non-empty column that is not NIM and not a class name
            // Class names look like "TI - 1A" or "IK - 1B"
            $reversed = array_reverse($row);
            foreach ($reversed as $col) {
                $val = trim((string) $col);
                if ($val === '') {
                    continue;
                }
                // Skip if it looks like NIM
                if (preg_match('/^\d+\.\d+\.\d+\.\d+\.\d+$/', $val)) {
                    continue;
                }
                // Skip if it looks like a class name (e.g., "TI - 1A", "IK - 2B", "IK - 1E (MSU)")
                if (preg_match('/^(TI|IK)\s*-\s*[1-4][A-E]/', $val)) {
                    continue;
                }
                // Skip if it's just a number (row counter)
                if (is_numeric($val) && (int)$val >= 1 && (int)$val <= 30) {
                    // Check if we haven't found NIM yet - this might be row number
                    if ($nim === null) {
                        continue;
                    }
                }
                // This should be the name
                $nama = $val;
                break;
            }

            // Skip rows without valid NIM or name
            if ($nim === null || $nama === null || $nama === '') {
                continue;
            }

            // Password = NIM without dots (e.g., "4.33.25.0.01" → "43325001")
            $password = str_replace('.', '', $nim);

            // Use firstOrCreate to avoid race conditions
            $user = User::firstOrCreate(
                ['nim' => $nim],
                [
                    'name'     => $nama,
                    'email'    => null,
                    'password' => Hash::make($password),
                    'role'     => 'mahasiswa',
                ]
            );

            if ($user->wasRecentlyCreated) {
                if ($label === 'TI') {
                    $this->mhsTiCount++;
                } else {
                    $this->mhsIkCount++;
                }
            } else {
                $this->line("  Skip (sudah ada): {$nama} (NIM: {$nim})");
            }
        }

        fclose($handle);

        $count = $label === 'TI' ? $this->mhsTiCount : $this->mhsIkCount;
        $this->info("  Total Mahasiswa {$label}: {$count}");
    }
}
