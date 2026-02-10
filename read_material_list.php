<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'ECN Project Tracker No Formula 2.xlsx';

$spreadsheet = IOFactory::load($file);

// Read the "Material List" sheet
$sheet = $spreadsheet->getSheetByName('Material List');

if ($sheet === null) {
    echo "Material List sheet not found!\n";
    exit(1);
}

$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();

echo "Material List Sheet\n";
echo str_repeat("=", 120) . "\n";
echo "Dimensions: $highestColumn$highestRow (Total Rows: $highestRow)\n\n";

// Get headers
echo "Column Headers:\n";
$headers = [];
$headerRow = 1;
for ($col = 'A'; $col <= $highestColumn; $col++) {
    $value = $sheet->getCell($col . $headerRow)->getValue();
    $headers[$col] = $value;
    if (!empty($value)) {
        echo "  $col: $value\n";
    }
}
echo "\n";

// Show sample data (first 10 rows)
echo "Sample Data (First 10 rows):\n";
echo str_repeat("-", 120) . "\n\n";

for ($row = 2; $row <= min(11, $highestRow); $row++) {
    echo "Row $row:\n";
    $rowData = [];
    foreach ($headers as $col => $header) {
        if (!empty($header)) {
            $value = $sheet->getCell($col . $row)->getFormattedValue();
            if (!empty($value)) {
                $rowData[$header] = $value;
                echo "  $header: $value\n";
            }
        }
    }
    echo "\n";
}

echo str_repeat("=", 120) . "\n";
echo "Total data rows: " . ($highestRow - 1) . "\n";
