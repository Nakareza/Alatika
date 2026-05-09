# Hasil Fetching Data Excel — 9 Mei 2026

Ringkasan singkat
- Folder import: `d:\Alatika\inventaris-import`
- Master CSV: `storage/app/inventaris/master_inventaris.csv`
- Tabel DB: `inventaris` (`app/Models/Inventaris.php`)
- Total baris dimasukkan ke DB: 961
- Baris dengan `nama_alat = 'UNKNOWN'`: 643
- File diproses (16):
  - ALAT (Autosaved) br(2) (1).xlsx — total 10 — UNKNOWN 0
  - ALAT (Autosaved) br(2).xlsx — total 10 — UNKNOWN 0
  - Access Point (MG TU)(1).xlsx — total 12 — UNKNOWN 12
  - Arduino1.xlsx — total 11 — UNKNOWN 0
  - Book1(1).xlsx — total 18 — UNKNOWN 1
  - DATA INVENTARIS LEMARI RUANGAN JARKOM SB 2(1).xlsx — total 37 — UNKNOWN 3
  - Data Lab SB II, 2026.xlsx — total 179 — UNKNOWN 40
  - Data Raspberry&Perlengkapannya(4).xlsx — total 14 — UNKNOWN 1
  - KABEL 3D.xlsx — total 11 — UNKNOWN 0
  - KABEL HDMI.xlsx — total 8 — UNKNOWN 0
  - Komponen Robot(1).xlsx — total 43 — UNKNOWN 43
  - Power supply.xlsx — total 11 — UNKNOWN 0
  - stop kontak binus(3).xlsx — total 33 — UNKNOWN 0
  - stop kontak binus(4).xlsx — total 10 — UNKNOWN 0
  - stop kontak binus(5).xlsx — total 10 — UNKNOWN 0
  - Tolkit.xlsx — total 9 — UNKNOWN 0

Perubahan dan artefak yang dibuat
- Migration: `database/migrations/2026_05_09_000001_create_inventaris_table.php` (tabel `inventaris`) — sudah migrate.
- Model: `app/Models/Inventaris.php` (casts untuk `perlengkapan_detail`, `is_borrowable`).
- Controller (admin): `app/Http/Controllers/Admin/InventarisAdminController.php` dan view `resources/views/admin/inventaris/index.blade.php`.
- Artisan command impor: `app/Console/Commands/ImportInventaris.php` (signature: `inventaris:import {source?}`) — membaca CSV/XLSX, normalisasi, menulis `master_inventaris.csv`, dan memasukkan/ memperbarui DB.
- Skrip bantu (PHP):
  - `scripts/clean_inventaris.py` (ETL alternatif Python)
  - `scripts/dump_header.php` (tampilkan header baris pertama)
  - `scripts/dump_rows.php` (tampilkan N baris pertama)
  - `scripts/report_master_by_source.php` (laporan per-file dari master CSV)
- Composer package: `phpoffice/phpspreadsheet` ditambahkan untuk membaca XLSX/CSV.
- Folder import dibuat: `d:\Alatika\inventaris-import` (tempat letakkan file baru).

Masalah yang ditemukan
- Banyak file memiliki tata letak non-standar (header di baris >1, label dan nilai terpisah di kolom sebelah), menyebabkan heuristik otomatis belum mampu menangkap `nama_alat` untuk beberapa file.
- File bermasalah contoh:
  - `Access Point (MG TU)(1).xlsx` — semua baris UNKNOWN (12/12).
  - `Komponen Robot(1).xlsx` — semua baris UNKNOWN (43/43).
  - `Data Lab SB II, 2026.xlsx` — 40/179 baris UNKNOWN.

Langkah yang sudah saya ambil untuk mengurangi masalah
- Tambah heuristik deteksi header: mencari baris header non-kosong yang mengandung minimal 2 kata kunci header (no, nama, kode, jumlah, brand, merk, keterangan).
- Perluas alias header untuk `nama_alat` (term: `brand`, `merk`, `keterangan`, dll.).
- Normalisasi header menangani nilai null/sel kosong.

Instruksi untuk melanjutkan besok (langkah-langkah praktis)
1) Jika Anda menaruh file baru, letakkan di `d:\Alatika\inventaris-import`.
2) Jalankan import penuh:

```bash
php artisan inventaris:import
```

3) Lihat laporan per-file (cepat):

```bash
php d:\Alatika\scripts\report_master_by_source.php
```

4) Jika file tertentu masih banyak `UNKNOWN`, jalankan inspeksi awal pada file itu untuk melihat baris header dan struktur (contoh 8 baris):

```bash
php d:\Alatika\scripts\dump_rows.php "d:\Alatika\inventaris-import\NamaFile.xlsx" 8
```

5) Kirimkan output `dump_rows.php` untuk file bermasalah kepada saya, atau sebutkan kolom mana yang berisi nama alat (mis. kolom D), maka saya akan tambahkan mapping khusus untuk file tersebut ke `ImportInventaris.php`.

6) Alternatif cepat: kita bisa membuat konfigurasi mapping per-file (JSON) yang menyatakan: `{"file_pattern":"Komponen Robot","name_column":"C"}` — saya bisa implementasikan ini jika Anda mau.

Catatan teknis singkat
- Master CSV ditulis di: `storage/app/inventaris/master_inventaris.csv`.
- Untuk memeriksa jumlah yang masuk ke DB:

```bash
php artisan tinker --execute="echo \App\Models\Inventaris::count();"
php artisan tinker --execute="echo \App\Models\Inventaris::where('nama_alat','UNKNOWN')->count();"
```

- Untuk menampilkan sampel teratasi:

```bash
php artisan tinker --execute="echo json_encode(\App\Models\Inventaris::where('nama_alat','<>','UNKNOWN')->limit(15)->get()->toArray(), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);"
```

Rekomendasi prioritas untuk iterasi selanjutnya
1. Tambah mapping per-file (konfigurasi JSON) untuk file yang pola kolomnya konsisten tetapi tidak standar.
2. Jika file sumber sering berisi metadata di atas tabel, pertimbangkan aturan deteksi header berbasis regex/kombinasi posisi kolom (mis. jika kolom D berisi "NAMA ALAT" gunakan baris itu sebagai header).
3. Tambah pengecualian otomatis untuk baris yang nampak seperti ringkasan (kata-kata `JUMLAH`, `Total`) agar tidak dimasukkan.

--
File ini dibuat otomatis oleh skrip pada repository.
Untuk membuka file ini: [docs/hasil_fetching_data_excel_2026-05-09.md](docs/hasil_fetching_data_excel_2026-05-09.md)
