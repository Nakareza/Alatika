<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use App\Models\Alat;
use App\Models\ToolSet;
use App\Models\ToolSetDetail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class InventarisImportSeeder extends Seeder
{
    public function run(): void
    {
        echo "Starting import seeder...\n";

        $logs = [
            'errors' => [],
            'warnings' => [],
            'success' => [],
        ];

        // 1. IMPORT KATEGORI
        $kategoriCsvPath = 'd:\Alatika\Rekap_kategori.csv';
        if (!file_exists($kategoriCsvPath)) {
            $logs['errors'][] = "File Rekap_kategori.csv not found at: {$kategoriCsvPath}";
            $this->printLogs($logs);
            return;
        }

        $kategoriMap = [];
        $handle = fopen($kategoriCsvPath, 'r');
        // skip header
        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row) || count($row) < 2) continue;
            
            $namaKategori = trim($row[1]);
            $deskripsi = isset($row[5]) ? trim($row[5]) : null;

            if (empty($namaKategori)) {
                $logs['warnings'][] = "Empty category name at row: " . json_encode($row);
                continue;
            }

            $kategori = Kategori::updateOrCreate(
                ['nama_kategori' => $namaKategori],
                ['deskripsi' => $deskripsi]
            );

            $kategoriMap[strtolower($namaKategori)] = $kategori->id;
        }
        fclose($handle);
        echo "Imported " . count($kategoriMap) . " categories.\n";

        // 2. IMPORT ALAT UTAMA & TOOL SETS
        $alatCsvPath = 'd:\Alatika\data_alat_utama.csv';
        if (!file_exists($alatCsvPath)) {
            $logs['errors'][] = "File data_alat_utama.csv not found at: {$alatCsvPath}";
            $this->printLogs($logs);
            return;
        }

        $handle = fopen($alatCsvPath, 'r');
        // skip header
        fgetcsv($handle);

        $processedCodes = [];
        $processedNames = [];
        
        // Counter for code barang generation per year
        $yearCounters = [];

        // Pre-populate counters with existing code_barang to avoid conflicts
        $existingAlats = Alat::whereNotNull('kode_barang')->get();
        foreach ($existingAlats as $ea) {
            $parts = explode('-', $ea->kode_barang);
            if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                $yr = (int) $parts[0];
                $seq = (int) $parts[1];
                if (!isset($yearCounters[$yr]) || $seq > $yearCounters[$yr]) {
                    $yearCounters[$yr] = $seq;
                }
            }
        }

        $rowCount = 0;
        $insertedAlat = 0;
        $insertedToolSets = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $rowCount++;
            if (empty($row) || count($row) < 3) continue;

            $no = trim($row[0]);
            $kategoriName = trim($row[1]);
            $namaAlat = trim($row[2]);
            $brand = trim($row[3]);
            $kodeAlat = trim($row[4]);
            $tahun = trim($row[5]);
            $spesifikasi = trim($row[6]);
            $kondisi = trim($row[7]);
            $keterangan = trim($row[8]);

            // Skip separator rows (e.g. ▌ ACCESS POINT)
            if (empty($no) && (empty($namaAlat) || str_contains($kategoriName, '▌'))) {
                continue;
            }

            // Validasi data kosong
            if (empty($namaAlat)) {
                $logs['warnings'][] = "Row {$rowCount}: Nama alat is empty. Row: " . json_encode($row);
                continue;
            }

            if (empty($kategoriName)) {
                $logs['warnings'][] = "Row {$rowCount}: Kategori is empty. Row: " . json_encode($row);
                continue;
            }

            $kategoriId = $kategoriMap[strtolower($kategoriName)] ?? null;
            if (!$kategoriId) {
                $logs['warnings'][] = "Row {$rowCount}: Kategori '{$kategoriName}' not found in categories. Row: " . json_encode($row);
                continue;
            }

            // Clean up year
            $tahunInt = null;
            if (!empty($tahun) && is_numeric($tahun)) {
                $tahunInt = (int) $tahun;
            }

            // 2A. PROCESS TOOL SETS
            if (strtolower($kategoriName) === 'tool set') {
                if (empty($kodeAlat)) {
                    $logs['warnings'][] = "Row {$rowCount}: Tool Set does not have a code. Row: " . json_encode($row);
                    continue;
                }

                // Check duplicate Tool Set code in database or current run
                if (in_array($kodeAlat, $processedCodes)) {
                    $logs['warnings'][] = "Row {$rowCount}: Duplicate Tool Set code '{$kodeAlat}' in file.";
                    continue;
                }
                $processedCodes[] = $kodeAlat;

                $toolSet = ToolSet::updateOrCreate(
                    ['kode_tool_set' => $kodeAlat],
                    [
                        'nama_tool_set' => $namaAlat,
                        'kategori_id' => $kategoriId,
                        'stok' => 1,
                        'stok_tersedia' => 1,
                        'lokasi' => 'Lab TI', // Default lokasi
                        'kondisi' => $this->cleanKondisi($kondisi),
                        'keterangan' => $keterangan,
                        'tahun' => $tahunInt ?: 2012,
                    ]
                );
                $insertedToolSets++;
                continue;
            }

            // 2B. PROCESS MAIN INVENTORY (ALAT)
            // Parse stock from spesifikasi if it contains "Jumlah: X" (e.g. for Komponen Robot)
            $stock = 1;
            if (preg_match('/Jumlah:\s*(\d+)/i', $spesifikasi, $matches)) {
                $stock = (int) $matches[1];
            }

            // Check duplicate in the file
            if (!empty($kodeAlat) && $kodeAlat !== '-') {
                $dupKey = $kodeAlat;
                if (in_array($dupKey, $processedCodes)) {
                    $logs['warnings'][] = "Row {$rowCount}: Duplicate kode '{$kodeAlat}' in file. Skipping to prevent duplicate records.";
                    continue;
                }
                $processedCodes[] = $dupKey;
            } else {
                // If code is empty, use name + category to check duplicate in the file
                $dupKey = strtolower($namaAlat . '|' . $kategoriName);
                if (in_array($dupKey, $processedNames)) {
                    $logs['warnings'][] = "Row {$rowCount}: Duplicate item name '{$namaAlat}' without code. Skipping to prevent duplicates.";
                    continue;
                }
                $processedNames[] = $dupKey;
            }

            // Check if record already exists in database
            $existingAlat = null;
            if (!empty($kodeAlat) && $kodeAlat !== '-') {
                $existingAlat = Alat::where('kode', $kodeAlat)->first();
            } else {
                $existingAlat = Alat::where('nama', $namaAlat)
                    ->where('kategori_id', $kategoriId)
                    ->first();
            }

            $kodeBarang = null;
            if ($existingAlat && !empty($existingAlat->kode_barang)) {
                // Keep existing code_barang for idempotency
                $kodeBarang = $existingAlat->kode_barang;
            } else {
                // Determine year for code_barang
                $targetYear = $tahunInt;
                if (!$targetYear) {
                    $targetYear = rand(2023, 2025);
                }

                // Increment sequential code for this year
                if (!isset($yearCounters[$targetYear])) {
                    $yearCounters[$targetYear] = 0;
                }
                $yearCounters[$targetYear]++;
                $kodeBarang = $targetYear . '-' . str_pad($yearCounters[$targetYear], 4, '0', STR_PAD_LEFT);
            }

            // Perform final insert or update
            Alat::updateOrCreate(
                [
                    'id' => $existingAlat ? $existingAlat->id : null,
                ],
                [
                    'nama' => $namaAlat,
                    'kode' => (!empty($kodeAlat) && $kodeAlat !== '-') ? $kodeAlat : $kodeBarang, // Use code_barang as fallback for code or when it is '-'
                    'kategori_id' => $kategoriId,
                    'stok_total' => $stock,
                    'stok_tersedia' => $existingAlat ? $existingAlat->stok_tersedia : $stock,
                    'stok_maintenance' => $existingAlat ? $existingAlat->stok_maintenance : 0,
                    'lokasi' => 'Lab TI',
                    'deskripsi' => $spesifikasi ?: $keterangan,
                    'status' => 'tersedia',
                    'kondisi' => $this->cleanKondisi($kondisi),
                    'program_studi' => null, // Default
                    'tahun_pengadaan' => $tahunInt,

                    // New Columns
                    'kode_barang' => $kodeBarang,
                    'nama_barang' => $namaAlat,
                    'merk' => $brand ?: null,
                    'spesifikasi' => $spesifikasi ?: null,
                    'stok' => $stock,
                    'satuan' => 'Unit',
                    'tahun' => $tahunInt,
                    'keterangan' => $keterangan ?: null,
                ]
            );

            $insertedAlat++;
        }
        fclose($handle);
        echo "Imported {$insertedAlat} main inventory items.\n";
        echo "Imported {$insertedToolSets} Tool Sets.\n";

        // 3. IMPORT TOOL SET DETAILS
        $detailCsvPath = 'd:\Alatika\Tool_set_detail.csv';
        if (!file_exists($detailCsvPath)) {
            $logs['errors'][] = "File Tool_set_detail.csv not found at: {$detailCsvPath}";
            $this->printLogs($logs);
            return;
        }

        $handle = fopen($detailCsvPath, 'r');
        fgetcsv($handle); // skip header

        $detailCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row) || count($row) < 4) continue;

            $kodeSet = trim($row[1]);
            $namaKomponen = trim($row[3]);
            $brand = trim($row[4]);
            $jumlah = (int) trim($row[5]);
            $satuan = trim($row[6]);
            $keadaan = trim($row[8]);

            if (empty($kodeSet) || empty($namaKomponen)) continue;

            $toolSet = ToolSet::where('kode_tool_set', $kodeSet)->first();
            if (!$toolSet) {
                $logs['warnings'][] = "Component row: Tool Set code '{$kodeSet}' not found. Skipping component '{$namaKomponen}'.";
                continue;
            }

            ToolSetDetail::updateOrCreate(
                [
                    'tool_set_id' => $toolSet->id,
                    'nama_komponen' => $namaKomponen,
                ],
                [
                    'jumlah' => $jumlah,
                    'satuan' => $satuan ?: 'Buah',
                    'keterangan' => $brand ?: null, // Keep brand as keterangan
                ]
            );
            $detailCount++;
        }
        fclose($handle);
        echo "Imported {$detailCount} Tool Set component details.\n";

        $this->printLogs($logs);
    }

    private function printLogs(array $logs): void
    {
        echo "\n=== IMPORT SEEDER LOGS ===\n";
        if (!empty($logs['errors'])) {
            echo "ERRORS:\n";
            foreach ($logs['errors'] as $err) {
                echo "- " . $err . "\n";
            }
        }
        if (!empty($logs['warnings'])) {
            echo "WARNINGS / VALIDATION ISSUES:\n";
            foreach ($logs['warnings'] as $warn) {
                echo "- " . $warn . "\n";
            }
        }
        echo "=== END LOGS ===\n";
    }

    private function cleanKondisi(?string $kondisi): string
    {
        $kondisi = strtolower(trim((string)$kondisi));
        if (empty($kondisi)) {
            return 'perlu_pengecekan';
        }
        if (str_contains($kondisi, 'baik')) {
            if (str_contains($kondisi, 'cek') || str_contains($kondisi, 'komponen')) {
                return 'perlu_pengecekan';
            }
            return 'baik';
        }
        if (str_contains($kondisi, 'rusak') || str_contains($kondisi, 'tidak ada') || str_contains($kondisi, 'masalah') || str_contains($kondisi, 'rusak sebagian')) {
            return 'rusak';
        }
        return 'perlu_pengecekan';
    }
}
