<?php

namespace App\Console\Commands;

use App\Models\Inventaris;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Csv as CsvReader;

class ImportInventaris extends Command
{
    protected $signature = 'inventaris:import {source? : File CSV/XLSX atau folder berisi file inventaris}';

    protected $description = 'Normalisasi file CSV/XLSX inventaris menjadi master_inventaris.csv dan simpan ke tabel inventaris';

    public function handle(): int
    {
        $providedSource = $this->argument('source');
        $sourcePath = is_string($providedSource) && $providedSource !== ''
            ? $providedSource
            : base_path('inventaris-import');

        if (!$providedSource && !File::exists($sourcePath)) {
            File::ensureDirectoryExists($sourcePath);
            $this->warn("Folder default dibuat: {$sourcePath}. Letakkan file CSV/XLSX di folder ini lalu jalankan ulang command.");
            return self::SUCCESS;
        }

        if (!File::exists($sourcePath)) {
            $this->error("Source tidak ditemukan: {$sourcePath}");
            return self::FAILURE;
        }

        $files = File::isDirectory($sourcePath)
            ? collect(File::files($sourcePath))->filter(fn ($file) => in_array(strtolower($file->getExtension()), ['csv', 'xlsx', 'xls']))
            : collect([new \SplFileInfo($sourcePath)]);

        if ($files->isEmpty()) {
            $this->warn('Tidak ada file CSV/XLSX yang ditemukan.');
            return self::SUCCESS;
        }

        $normalizedRows = [];

        foreach ($files as $file) {
            $path = $file instanceof \SplFileInfo ? $file->getPathname() : $file->getRealPath();
            if (!$path) {
                continue;
            }

            foreach ($this->readRows($path) as $rowIndex => $row) {
                $normalized = $this->normalizeRow($row, basename($path), $rowIndex + 1);
                if ($normalized) {
                    $normalizedRows[] = $normalized;
                }
            }
        }

        if (empty($normalizedRows)) {
            $this->warn('Tidak ada data valid yang berhasil dinormalisasi.');
            return self::SUCCESS;
        }

        $outputDir = storage_path('app/inventaris');
        File::ensureDirectoryExists($outputDir);
        $outputCsv = $outputDir . DIRECTORY_SEPARATOR . 'master_inventaris.csv';


        $handle = fopen($outputCsv, 'w');
        $csvHeaders = [
            'source_file', 'source_row', 'nama_alat', 'kategori', 'kode_barang', 'jumlah_stok',
            'lokasi_simpan', 'tahun_perolehan', 'kondisi', 'perlengkapan_detail', 'is_borrowable'
        ];

        fputcsv($handle, $csvHeaders);

        foreach ($normalizedRows as $row) {
            $line = [];
            foreach ($csvHeaders as $h) {
                $line[] = $row[$h] ?? '';
            }
            fputcsv($handle, $line);
        }

        fclose($handle);

        $this->info('Master CSV berhasil dibuat: ' . $outputCsv);

        $imported = 0;
        foreach ($normalizedRows as $row) {
            $payload = [
                'nama_alat' => $row['nama_alat'],
                'kategori' => $row['kategori'],
                'kode_barang' => $row['kode_barang'] ?: null,
                'jumlah_stok' => (int) $row['jumlah_stok'],
                'lokasi_simpan' => $row['lokasi_simpan'] ?: null,
                'tahun_perolehan' => $row['tahun_perolehan'] !== '' ? (int) $row['tahun_perolehan'] : null,
                'kondisi' => $row['kondisi'],
                'perlengkapan_detail' => $row['perlengkapan_detail'] !== '' ? json_decode($row['perlengkapan_detail'], true) : null,
                'is_borrowable' => (bool) $row['is_borrowable'],
            ];

            if ($payload['kode_barang']) {
                Inventaris::updateOrCreate(['kode_barang' => $payload['kode_barang']], $payload);
            } else {
                Inventaris::create($payload);
            }

            $imported++;
        }

        $this->info("Import selesai. Total baris diproses: {$imported}");

        return self::SUCCESS;
    }

    private function readRows(string $path): array
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($extension === 'csv') {
            $reader = new CsvReader();
            $reader->setDelimiter($this->detectDelimiter($path));
            $spreadsheet = $reader->load($path);
        } else {
            $spreadsheet = IOFactory::load($path);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        if (empty($rows)) {
            return [];
        }

        // Find the first non-empty row to use as header (many sheet exports have leading metadata rows)
        $headerRow = null;
        $headerKeywords = ['no', 'nama', 'nama alat', 'brand', 'kode', 'kode barang', 'kode_alat', 'jumlah', 'qty', 'quantity', 'merk', 'keterangan', 'kondisi', 'lokasi'];

        while (!empty($rows)) {
            $candidate = array_shift($rows);

            $hasNonEmpty = false;
            foreach ($candidate as $cell) {
                if ($cell !== null && trim((string) $cell) !== '') {
                    $hasNonEmpty = true;
                    break;
                }
            }

            if (!$hasNonEmpty) {
                continue;
            }

            // Heuristic: if the candidate row contains at least two header-like cells, use it as header
            $normalizedCells = array_map(fn($v) => $this->normalizeHeader((string) $v), $candidate);
            $matchCount = 0;
            foreach ($normalizedCells as $nc) {
                if (in_array($nc, $headerKeywords, true)) {
                    $matchCount++;
                }
            }

            if ($matchCount >= 2) {
                $headerRow = $candidate;
                break;
            }

            // otherwise keep searching for a better header row
        }

        if ($headerRow === null) {
            return [];
        }
        $headers = [];

        foreach ($headerRow as $column => $value) {
            $headers[$column] = $this->normalizeHeader($value);
        }

        $result = [];
        foreach ($rows as $row) {
            $mapped = [];
            foreach ($row as $column => $value) {
                $header = $headers[$column] ?? null;
                if ($header) {
                    $mapped[$header] = is_scalar($value) ? trim((string) $value) : '';
                }
            }

            if (!empty(array_filter($mapped, fn ($value) => $value !== ''))) {
                $result[] = $mapped;
            }
        }

        return $result;
    }

    private function normalizeRow(array $row, string $sourceFile, int $sourceRow): ?array
    {
        $name = $this->firstValue($row, ['nama_alat', 'nama barang', 'nama_barang', 'nama', 'barang', 'item', 'alat', 'uraian', 'deskripsi', 'brand', 'merk', 'keterangan']) ?: 'UNKNOWN';
        $kategori = $this->firstValue($row, ['kategori', 'jenis', 'kelompok', 'group', 'category']) ?: 'Umum';
        $kode = $this->firstValue($row, ['kode_barang', 'kode barang', 'kode', 'kode_alat', 'serial number', 'serial', 'sn', 'mac', 'barcode']);
        $jumlah = $this->parseInt($this->firstValue($row, ['jumlah', 'qty', 'quantity', 'stok', 'stock', 'jumlah stok']), 1);
        $lokasi = $this->firstValue($row, ['lokasi', 'lokasi_simpan', 'rak', 'lemari', 'ruang', 'tempat']);
        $tahun = $this->parseYear($this->firstValue($row, ['tahun', 'tahun_perolehan', 'year', 'thn']));
        $kondisi = $this->normalizeCondition($this->firstValue($row, ['kondisi', 'status', 'keadaan']) ?: 'Baik');
        $detail = $this->parseDetail($this->firstValue($row, ['perlengkapan', 'kelengkapan', 'aksesoris', 'accessories', 'detail', 'detail_kelengkapan']));

        $isBorrowableRaw = $this->firstValue($row, ['is_borrowable', 'borrowable', 'bisa_pinjam', 'bisa dipinjam', 'status_pinjaman']);
        $isBorrowable = $this->parseBool($isBorrowableRaw);
        if ($isBorrowable === null) {
            $isBorrowable = !$this->looksStaticAsset($name . ' ' . $kategori . ' ' . $kode);
        }

        return [
            'source_file' => $sourceFile,
            'source_row' => $sourceRow,
            'nama_alat' => $name,
            'kategori' => $kategori,
            'kode_barang' => $kode,
            'jumlah_stok' => (string) max(1, $jumlah),
            'lokasi_simpan' => $lokasi,
            'tahun_perolehan' => $tahun ? (string) $tahun : '',
            'kondisi' => $kondisi,
            'perlengkapan_detail' => $detail ? json_encode($detail, JSON_UNESCAPED_UNICODE) : '',
            'is_borrowable' => $isBorrowable ? '1' : '0',
        ];
    }

    private function normalizeHeader(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        $value = Str::of($value)->lower()->replace(['_', '-', '.', ':'], ' ')->squish()->toString();
        $value = preg_replace('/[^\pL\pN\s\/]+/u', '', $value) ?: $value;
        return trim($value);
    }

    private function firstValue(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            $key = $this->normalizeHeader($key);
            foreach ($row as $column => $value) {
                if ($this->normalizeHeader((string) $column) === $key && trim((string) $value) !== '') {
                    return trim((string) $value);
                }
            }
        }

        return '';
    }

    private function parseInt(string $value, int $default = 1): int
    {
        $value = preg_replace('/[^\d-]/', '', $value);
        return is_numeric($value) ? (int) $value : $default;
    }

    private function parseYear(?string $value): ?int
    {
        if (!$value) {
            return null;
        }

        $year = $this->parseInt($value, 0);
        return ($year >= 1900 && $year <= 2100) ? $year : null;
    }

    private function parseBool(?string $value): ?bool
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        $value = Str::of($value)->lower()->squish()->toString();

        return match ($value) {
            '1', 'true', 'ya', 'yes', 'borrowable', 'bisa pinjam', 'dipinjam' => true,
            '0', 'false', 'tidak', 'no', 'non borrowable', 'aset statis', 'statis' => false,
            default => null,
        };
    }

    private function looksStaticAsset(string $text): bool
    {
        $text = Str::of($text)->lower()->toString();

        foreach (['ac', 'proyektor', 'projector', 'fasilitas ruangan', 'aset statis', 'instalasi', 'terpasang', 'fixed', 'permanen'] as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function parseDetail(?string $value): ?array
    {
        if (!$value || trim($value) === '') {
            return null;
        }

        $parts = preg_split('/[;,|\/]+/', $value) ?: [];
        $parts = array_values(array_filter(array_map('trim', $parts)));

        return $parts ?: null;
    }

    private function normalizeCondition(string $value): string
    {
        $value = Str::of($value)->lower()->squish()->toString();

        if (str_contains($value, 'rusak') || str_contains($value, 'repair') || str_contains($value, 'perbaikan')) {
            return 'Rusak';
        }

        if (str_contains($value, 'baik') || str_contains($value, 'normal') || str_contains($value, 'siap')) {
            return 'Baik';
        }

        return Str::of($value)->headline()->toString();
    }

    private function detectDelimiter(string $path): string
    {
        $sample = File::get($path);
        $sample = substr($sample, 0, 4096);

        $candidates = [',', ';', "\t", '|'];
        $bestDelimiter = ',';
        $bestCount = 0;

        foreach ($candidates as $delimiter) {
            $count = substr_count($sample, $delimiter);
            if ($count > $bestCount) {
                $bestCount = $count;
                $bestDelimiter = $delimiter;
            }
        }

        return $bestDelimiter;
    }
}
