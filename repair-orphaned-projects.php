#!/usr/bin/env php
<?php

/**
 * Repair Script: Fix Orphaned Projects
 * 
 * This script finds all projects that don't have corresponding workflow stage records
 * and creates the missing records with default values.
 * 
 * Usage: php repair-orphaned-projects.php
 * Or via artisan tinker: include('repair-orphaned-projects.php');
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Project;
use App\Models\ProjectWorkflowStage;

echo "Starting repair of orphaned projects...\n\n";

// Get all projects
$projects = Project::all();
$totalProjects = $projects->count();
$orphanedCount = 0;
$repairedCount = 0;
$orphanedProjects = [];

echo "Scanning {$totalProjects} projects...\n";

foreach ($projects as $project) {
    // Check if workflow stage exists
    $hasWorkflowStage = ProjectWorkflowStage::where('project_id', $project->project_id)->exists();
    
    if (!$hasWorkflowStage) {
        $orphanedProjects[] = $project->project_id;
        $orphanedCount++;
        
        // Create missing workflow stage
        try {
            ProjectWorkflowStage::create([
                'project_id' => $project->project_id,
                'workflow_stage' => Project::WORKFLOW_CLIENT_ENQUIRY,
                'client_enquiry_date' => $project->created_at ?? now(),
            ]);
            
            $repairedCount++;
            echo "✓ Repaired: {$project->project_id}\n";
        } catch (\Exception $e) {
            echo "✗ Failed to repair {$project->project_id}: {$e->getMessage()}\n";
        }
    }
}

echo "\n";
echo "===========================================\n";
echo "Repair Summary:\n";
echo "===========================================\n";
echo "Total Projects:        {$totalProjects}\n";
echo "Orphaned Projects:     {$orphanedCount}\n";
echo "Successfully Repaired: {$repairedCount}\n";
echo "Failed:                " . ($orphanedCount - $repairedCount) . "\n";
echo "===========================================\n";

if ($orphanedCount > 0) {
    echo "\nOrphaned Project IDs:\n";
    foreach (array_slice($orphanedProjects, 0, 20) as $projectId) {
        echo "  - {$projectId}\n";
    }
    if (count($orphanedProjects) > 20) {
        echo "  ... and " . (count($orphanedProjects) - 20) . " more\n";
    }
}

echo "\nDone!\n";
