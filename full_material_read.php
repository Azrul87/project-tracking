<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$file = 'ECN Project Tracker No Formula 2.xlsx';
$spreadsheet = IOFactory::load($file);
$sheet = $spreadsheet->getSheetByName('Material List');

if ($sheet === null) {
    echo "Material List sheet not found!\n";
    exit(1);
}

echo "MATERIAL LIST - COMPLETE DATA EXTRACTION\n";
echo str_repeat("=", 150) . "\n\n";

// Find the actual data range
$highestRow = $sheet->getHighestDataRow();
$highestColumn = $sheet->getHighestDataColumn();

echo "Data Range: A1 to {$highestColumn}{$highestRow}\n\n";

// Extract all data as a table
echo "First 15 rows of data:\n";
echo str_repeat("-", 150) . "\n";

for ($row = 1; $row <= min(15, $highestRow); $row++) {
    echo "Row $row: ";
    $rowData = [];
    
    // Check columns A through AK (as shown in your screenshot)
    foreach (range('A', 'Z') as $col) {
        $cell = $sheet->getCell($col . $row);
        $value = $cell->getFormattedValue();
        if ($value !== null && $value !== '') {
            $rowData[$col] = $value;
        }
    }
    
    // Add AA-AK
    foreach (['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK'] as $col) {
        $cell = $sheet->getCell($col . $row);
        $value = $cell->getFormattedValue();
        if ($value !== null && $value !== '') {
            $rowData[$col] = $value;
        }
    }
    
    if (empty($rowData)) {
        echo "[EMPTY ROW]\n";
    } else {
        foreach ($rowData as $col => $val) {
            echo "$col: " . substr($val, 0, 50) . " | ";
        }
        echo "\n";
    }
}

echo "\n" . str_repeat("=", 150) . "\n";
echo "Analysis complete. Total rows processed: $highestRow\n";
