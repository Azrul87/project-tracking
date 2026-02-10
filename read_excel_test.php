<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'ECN Project Tracker No Formula 2.xlsx';

echo "Reading Excel file: $file\n";
echo str_repeat("=", 80) . "\n\n";

$spreadsheet = IOFactory::load($file);
$sheetNames = $spreadsheet->getSheetNames();

echo "Sheet Names:\n";
foreach ($sheetNames as $index => $name) {
    echo "  [$index] $name\n";
}
echo "\n";

// Read the first sheet
$sheet = $spreadsheet->getSheet(0);
$highestRow = $sheet->getHighestRow();
$highestColumn = $sheet->getHighestColumn();

echo "First Sheet: " . $sheet->getTitle() . "\n";
echo "Dimensions: $highestColumn$highestRow\n";
echo str_repeat("-", 80) . "\n\n";

// Get headers (first row)
echo "Column Headers:\n";
$headers = [];
$columnIndex = 1;
foreach ($sheet->getRowIterator(1, 1) as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false);
    foreach ($cellIterator as $cell) {
        $value = $cell->getValue();
        $headers[] = $value;
        if (!empty($value)) {
            echo "  " . $cell->getColumn() . ": " . $value . "\n";
        }
        $columnIndex++;
    }
}
echo "\n";

// Show first 5 data rows
echo "First 5 Data Rows:\n";
echo str_repeat("-", 80) . "\n";

for ($row = 2; $row <= min(6, $highestRow); $row++) {
    echo "\nRow $row:\n";
    $colIndex = 0;
    foreach ($sheet->getRowIterator($row, $row) as $rowObj) {
        $cellIterator = $rowObj->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        foreach ($cellIterator as $cell) {
            $value = $cell->getFormattedValue();
            if (!empty($value) && isset($headers[$colIndex]) && !empty($headers[$colIndex])) {
                echo "  " . $headers[$colIndex] . ": " . $value . "\n";
            }
            $colIndex++;
        }
    }
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "Total Rows: $highestRow\n";
