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

echo "Material List Sheet - RAW DATA INSPECTION\n";
echo str_repeat("=", 120) . "\n\n";

// Check first 5 rows completely raw
for ($row = 1; $row <= min(5, $highestRow); $row++) {
    echo "Row $row:\n";
    for ($col = 'A'; $col <= 'Z'; $col++) {
        $cellValue = $sheet->getCell($col . $row)->getValue();
        if ($cellValue !== null && $cellValue !== '') {
            echo "  $col: [$cellValue]\n";
        }
    }
    echo "\n";
}

// Look for headers in different rows
echo "\nSearching for potential header row...\n";
for ($row = 1; $row <= 10; $row++) {
    $cellA = $sheet->getCell('A' . $row)->getValue();
    $cellB = $sheet->getCell('B' . $row)->getValue();
    $cellC = $sheet->getCell('C' . $row)->getValue();
    
    if (!empty($cellA)) {
        echo "Row $row: A=[$cellA], B=[$cellB], C=[$cellC]\n";
    }
}
