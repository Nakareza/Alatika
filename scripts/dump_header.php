<?php
require __DIR__ . '/../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$path = $argv[1] ?? null;
if (!$path || !file_exists($path)) {
    echo "File not found: $path\n";
    exit(2);
}

$spreadsheet = IOFactory::load($path);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);
$header = [];
if (!empty($rows)) {
    $first = array_shift($rows);
    foreach ($first as $col => $val) {
        $header[$col] = $val;
    }
}

echo json_encode($header, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) . PHP_EOL;