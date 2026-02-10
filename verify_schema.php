<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "DATABASE SCHEMA VERIFICATION\n";
echo str_repeat("=", 80) . "\n\n";

// 1. Check project_materials table
echo "1. PROJECT_MATERIALS TABLE\n";
echo str_repeat("-", 80) . "\n";
if (Schema::hasTable('project_materials')) {
    $columns = Schema::getColumnListing('project_materials');
    echo "✓ Table exists with " . count($columns) . " columns\n";
    echo "\nMaterial quantity columns:\n";
    $materialColumns = array_filter($columns, function($col) {
        return !in_array($col, ['id', 'project_id', 'remark', 'created_at', 'updated_at']);
    });
    foreach ($materialColumns as $col) {
        echo "  - $col\n";
    }
} else {
    echo "✗ Table does not exist\n";
}

echo "\n";

// 2. Check projects table for new fields
echo "2. PROJECTS TABLE - NEW FIELDS\n";
echo str_repeat("-", 80) . "\n";
$projectColumns = Schema::getColumnListing('projects');

$fieldsToCheck = ['module', 'module_quantity', 'inverter', 'roof_type', 'procurement_status'];
foreach ($fieldsToCheck as $field) {
    $exists = in_array($field, $projectColumns);
    echo ($exists ? "✓" : "✗") . " $field: " . ($exists ? "EXISTS" : "MISSING") . "\n";
}

echo "\n";

// 3. Check items table (should not have project-specific fields)
echo "3. ITEMS TABLE - VERIFICATION (Should be catalog only)\n";
echo str_repeat("-", 80) . "\n";
$itemColumns = Schema::getColumnListing('items');

$shouldNotExist = ['project_id', 'quantity', 'supplier', 'order_date'];
$foundProjectFields = array_intersect($shouldNotExist, $itemColumns);

if (empty($foundProjectFields)) {
    echo "✓ No project-specific fields found (correct)\n";
} else {
    echo "✗ Found project-specific fields (should be removed):\n";
    foreach ($foundProjectFields as $field) {
        echo "  - $field\n";
    }
}

echo "\nCurrent items table structure:\n";
foreach ($itemColumns as $col) {
    echo "  - $col\n";
}

echo "\n";
echo str_repeat("=", 80) . "\n";
echo "Schema verification complete!\n";
