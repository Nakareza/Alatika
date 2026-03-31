<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{
    public function run(): void
    {
        $alatData = [
            ['nama' => 'Arduino Uno R3',              'kode' => 'ARD-001', 'kategori' => 'Microcontroller',       'stok_total' => 15, 'stok_tersedia' => 12, 'lokasi' => 'Rak A1'],
            ['nama' => 'ESP32 DevKit V1',              'kode' => 'ESP-015', 'kategori' => 'Microcontroller',       'stok_total' => 20, 'stok_tersedia' => 14, 'lokasi' => 'Rak A2'],
            ['nama' => 'Raspberry Pi 4 Model B',       'kode' => 'RPI-008', 'kategori' => 'Microcontroller',       'stok_total' => 8,  'stok_tersedia' => 5,  'lokasi' => 'Rak A3'],
            ['nama' => 'Oscilloscope Digital',          'kode' => 'OSC-005', 'kategori' => 'Lab Equipment',         'stok_total' => 5,  'stok_tersedia' => 3,  'lokasi' => 'Rak B1'],
            ['nama' => 'Multimeter Digital',            'kode' => 'MUL-012', 'kategori' => 'Lab Equipment',         'stok_total' => 10, 'stok_tersedia' => 8,  'lokasi' => 'Rak B2'],
            ['nama' => 'Soldering Station',             'kode' => 'SOL-003', 'kategori' => 'Lab Equipment',         'stok_total' => 6,  'stok_tersedia' => 4,  'lokasi' => 'Rak B3'],
            ['nama' => 'Sensor DHT22',                  'kode' => 'SNS-022', 'kategori' => 'Sensor & Aktuator',     'stok_total' => 30, 'stok_tersedia' => 25, 'lokasi' => 'Rak C1'],
            ['nama' => 'Sensor Ultrasonik HC-SR04',     'kode' => 'SNS-004', 'kategori' => 'Sensor & Aktuator',     'stok_total' => 25, 'stok_tersedia' => 20, 'lokasi' => 'Rak C1'],
            ['nama' => 'Power Supply Digital',          'kode' => 'PWR-007', 'kategori' => 'Lab Equipment',         'stok_total' => 4,  'stok_tersedia' => 2,  'lokasi' => 'Rak B4'],
            ['nama' => 'Logic Analyzer 8ch',            'kode' => 'LGA-003', 'kategori' => 'Lab Equipment',         'stok_total' => 3,  'stok_tersedia' => 1,  'lokasi' => 'Rak B5'],
            ['nama' => 'Function Generator',            'kode' => 'FNG-002', 'kategori' => 'Lab Equipment',         'stok_total' => 4,  'stok_tersedia' => 3,  'lokasi' => 'Rak B6'],
            ['nama' => 'Breadboard Set',                'kode' => 'BRD-010', 'kategori' => 'Komponen Elektronik',   'stok_total' => 50, 'stok_tersedia' => 42, 'lokasi' => 'Rak D1'],
            ['nama' => 'Kabel Jumper Set (M-M/M-F/F-F)','kode' => 'JMP-020', 'kategori' => 'Komponen Elektronik',   'stok_total' => 40, 'stok_tersedia' => 35, 'lokasi' => 'Rak D2'],
            ['nama' => 'LED Kit Assorted',              'kode' => 'LED-030', 'kategori' => 'Komponen Elektronik',   'stok_total' => 60, 'stok_tersedia' => 55, 'lokasi' => 'Rak D3'],
            ['nama' => 'Resistor Kit (1/4W)',           'kode' => 'RES-040', 'kategori' => 'Komponen Elektronik',   'stok_total' => 100,'stok_tersedia' => 95, 'lokasi' => 'Rak D4'],
        ];

        foreach ($alatData as $alat) {
            Alat::updateOrCreate(
                ['kode' => $alat['kode']],
                array_merge($alat, ['status' => 'tersedia'])
            );
        }
    }
}
