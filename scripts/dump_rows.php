<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$path = $argv[1] ?? null;
$limit = (int) ($argv[2] ?? 5);
if (!$path || !file_exists($path)) {
    echo "File not found: $path\n";
    exit(2);
}

$spreadsheet = IOFactory::load($path);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);
$out = [];
$idx = 0;
foreach ($rows as $r) {
    $idx++;
    $out[$idx] = $r;
    if ($idx >= $limit) break;
}

echo json_encode($out, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) . PHP_EOL;