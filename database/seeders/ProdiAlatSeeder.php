<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Seeder;

class ProdiAlatSeeder extends Seeder
{
    public function run(): void
    {
        $programStudi = 'D3 TI / D4 TRK';

        $alatData = [
            [
                'nama'           => 'Action Camera 360',
                'kode'           => 'CAM-001',
                'kategori'       => 'Kamera',
                'program_studi'  => $programStudi,
                'stok_total'     => 1,
                'stok_tersedia'  => 1,
                'lokasi'         => 'Lab Terpadu',
                'deskripsi'      => 'Merek: Insta | Insta360 X5 8K Action Camera 360 AI, Standard Package, Garansi Resmi',
                'status'         => 'tersedia',
                'kondisi'        => 'baik',
            ],
            [
                'nama'           => 'Watchy V2.0 - ESP32',
                'kode'           => 'WTC-002',
                'kategori'       => 'Microcontroller',
                'program_studi'  => $programStudi,
                'stok_total'     => 5,
                'stok_tersedia'  => 5,
                'lokasi'         => 'Lab Terpadu',
                'deskripsi'      => 'Merek: Watchy | Watchy ESP32 Programmable Electronic Watch, Ink Screen, Ultra-low Power Consumption',
                'status'         => 'tersedia',
                'kondisi'        => 'baik',
            ],
            [
                'nama'           => 'DJI Mini 4 Pro',
                'kode'           => 'DJI-003',
                'kategori'       => 'Drone',
                'program_studi'  => $programStudi,
                'stok_total'     => 2,
                'stok_tersedia'  => 2,
                'lokasi'         => 'Lab Terpadu',
                'deskripsi'      => 'Merek: DJI | DJI Mini 4 Pro (DJI RC-N2), Camera Drone, 4K Video, Under 249g',
                'status'         => 'tersedia',
                'kondisi'        => 'baik',
            ],
            [
                'nama'           => 'CANON EOS M50',
                'kode'           => 'CAN-004',
                'kategori'       => 'Kamera',
                'program_studi'  => $programStudi,
                'stok_total'     => 2,
                'stok_tersedia'  => 2,
                'lokasi'         => 'Lab Terpadu',
                'deskripsi'      => 'Merek: Canon | CANON EOS M50 Kit 15-45mm IS STM, Bonus 6 Item, Memory 32GB',
                'status'         => 'tersedia',
                'kondisi'        => 'baik',
            ],
            [
                'nama'           => 'DJI Mic Mini',
                'kode'           => 'DJI-005',
                'kategori'       => 'Audio',
                'program_studi'  => $programStudi,
                'stok_total'     => 2,
                'stok_tersedia'  => 2,
                'lokasi'         => 'Lab Terpadu',
                'deskripsi'      => 'Merek: DJI | DJI Mic Mini (2 TX + 1 RX + Charging Case), Wireless Microphone System',
                'status'         => 'tersedia',
                'kondisi'        => 'baik',
            ],
            [
                'nama'           => 'DJI Osmo Pocket 3 Basic Combo',
                'kode'           => 'DJI-006',
                'kategori'       => 'Kamera',
                'program_studi'  => $programStudi,
                'stok_total'     => 1,
                'stok_tersedia'  => 1,
                'lokasi'         => 'Lab Terpadu',
                'deskripsi'      => 'Merek: DJI | Sensor 1-inch CMOS, Layar 2 inch OLED, Video 4K, ActiveTrack 6.0, 3-Axis Gimbal Stabilizer, Bluetooth 5.2 & WiFi',
                'status'         => 'tersedia',
                'kondisi'        => 'baik',
            ],
            [
                'nama'           => 'Samsung Galaxy A16 5G',
                'kode'           => 'SAM-007',
                'kategori'       => 'Smartphone',
                'program_studi'  => $programStudi,
                'stok_total'     => 3,
                'stok_tersedia'  => 3,
                'lokasi'         => 'Lab Terpadu',
                'deskripsi'      => 'Merek: Samsung | Samsung Galaxy A16 5G, RAM 8GB, Storage 256GB, Garansi Resmi',
                'status'         => 'tersedia',
                'kondisi'        => 'baik',
            ],
            [
                'nama'           => 'Tablet HUAWEI MatePad 12 X',
                'kode'           => 'TAB-008',
                'kategori'       => 'Tablet',
                'program_studi'  => $programStudi,
                'stok_total'     => 2,
                'stok_tersedia'  => 2,
                'lokasi'         => 'Lab Terpadu',
                'deskripsi'      => 'Merek: Huawei | HUAWEI MatePad 12 X, PaperMatte Display, PC-level WPS Office, 66W SuperCharge, GoPaint',
                'status'         => 'tersedia',
                'kondisi'        => 'baik',
            ],
            [
                'nama'           => 'Smart TV LG 65" UHD 4K',
                'kode'           => 'TV-009',
                'kategori'       => 'Display',
                'program_studi'  => $programStudi,
                'stok_total'     => 3,
                'stok_tersedia'  => 3,
                'lokasi'         => 'Lab Terpadu',
                'deskripsi'      => 'Merek: LG | LG Smart TV UHD 4K, 65 Inch, Model 65UT8050',
                'status'         => 'tersedia',
                'kondisi'        => 'baik',
            ],
        ];

        foreach ($alatData as $alat) {
            Alat::updateOrCreate(
                ['kode' => $alat['kode']],
                $alat
            );
        }
    }
}
