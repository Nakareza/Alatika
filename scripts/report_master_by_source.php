<?php
$csv = fopen(__DIR__ . '/../storage/app/inventaris/master_inventaris.csv', 'r');
if (!$csv) {
    echo json_encode(['error' => 'master CSV not found']);
    exit(1);
}
$headers = fgetcsv($csv);
$index = array_flip($headers);
$data = [];
while (($row = fgetcsv($csv)) !== false) {
    $source = $row[$index['source_file']] ?? 'unknown_source';
    $name = $row[$index['nama_alat']] ?? '';
    if (!isset($data[$source])) {
        $data[$source] = [
            'source_file' => $source,
            'total' => 0,
            'unknown' => 0,
            'samples' => [],
        ];
    }
    $data[$source]['total']++;
    if (trim($name) === 'UNKNOWN' || trim($name) === '') {
        $data[$source]['unknown']++;
    } else {
        if (count($data[$source]['samples']) < 5) {
            $data[$source]['samples'][] = [
                'nama_alat' => $name,
                'kategori' => $row[$index['kategori']] ?? '',
                'kode_barang' => $row[$index['kode_barang']] ?? '',
                'jumlah_stok' => $row[$index['jumlah_stok']] ?? '',
                'lokasi_simpan' => $row[$index['lokasi_simpan']] ?? '',
            ];
        }
    }
}
ksort($data);
echo json_encode(array_values($data), JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
fclose($csv);
