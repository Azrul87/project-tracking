<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusSummaryController extends Controller
{
    /**
     * Display the status summary page
     */
    public function index(Request $request)
    {
        // Get filter statuses from request
        $selectedStatuses = $request->get('statuses', []);
        
        // Define status categories and their corresponding status types
        $categories = [
            'SEDA NEMCD' => [
                'status_type' => ProjectStatus::TYPE_SEDA_NEMCD,
                'statuses' => [
                    'Pending NEM Quota Submission',
                    'Pending NEM Quota Approval',
                    'Pending NEM Welcome Letter',
                ]
            ],
            'ST License' => [
                'status_type' => ProjectStatus::TYPE_ST_LICENSE,
                'statuses' => [
                    'Pending ST License Application',
                    'Pending ST License Approval',
                ]
            ],
            'LHDN Stamping (Contract Submission)' => [
                'status_type' => ProjectStatus::TYPE_LHDN_STAMPING,
                'statuses' => [
                    'Pending LHDN Stamping',
                ]
            ],
            'GITA Asset Application' => [
                'status_type' => ProjectStatus::TYPE_GITA_APPLICATION,
                'statuses' => [
                    'Pending GITA Application',
                ]
            ],
        ];

        // Calculate summary for each category
        $summary = [];
        $inProgressItems = [];

        foreach ($categories as $categoryName => $categoryData) {
            $statusType = $categoryData['status_type'];
            
            // Get latest status for each project (using subquery to get latest)
            // Total cases = all projects that have at least one status record for this type
            $total = DB::table('project_statuses')
                ->where('status_type', $statusType)
                ->select('project_id')
                ->distinct()
                ->count();
            
            // Cases in progress = projects with latest status = 'In Progress'
            $inProgress = DB::table('project_statuses as ps1')
                ->where('ps1.status_type', $statusType)
                ->where('ps1.status_value', ProjectStatus::STATUS_IN_PROGRESS)
                ->whereRaw('ps1.created_at = (
                    SELECT MAX(ps2.created_at) 
                    FROM project_statuses ps2 
                    WHERE ps2.project_id = ps1.project_id 
                    AND ps2.status_type = ps1.status_type
                )')
                ->distinct('ps1.project_id')
                ->count();
            
            // Cases completed = projects with latest status = 'Completed'
            $completed = DB::table('project_statuses as ps1')
                ->where('ps1.status_type', $statusType)
                ->where('ps1.status_value', ProjectStatus::STATUS_COMPLETED)
                ->whereRaw('ps1.created_at = (
                    SELECT MAX(ps2.created_at) 
                    FROM project_statuses ps2 
                    WHERE ps2.project_id = ps1.project_id 
                    AND ps2.status_type = ps1.status_type
                )')
                ->distinct('ps1.project_id')
                ->count();
            
            // Cases to be done = total - completed (includes pending and in progress)
            $toBeDone = $total - $completed;
            
            $summary[$categoryName] = [
                'total' => $total,
                'to_be_done' => $toBeDone,
                'in_progress' => $inProgress,
                'completed' => $completed,
                'status_type' => $statusType,
            ];

            // Get in-progress items for this category
            $inProgressProjectIds = DB::table('project_statuses as ps1')
                ->where('ps1.status_type', $statusType)
                ->where('ps1.status_value', ProjectStatus::STATUS_IN_PROGRESS)
                ->whereRaw('ps1.created_at = (
                    SELECT MAX(ps2.created_at) 
                    FROM project_statuses ps2 
                    WHERE ps2.project_id = ps1.project_id 
                    AND ps2.status_type = ps1.status_type
                )')
                ->pluck('ps1.project_id')
                ->toArray();

            $items = Project::whereIn('project_id', $inProgressProjectIds)
                ->select('project_id', 'name', 'client_id', 'location')
                ->get()
                ->map(function ($project) use ($categoryName, $statusType) {
                    return [
                        'project_id' => $project->project_id,
                        'name' => $project->name ?? 'N/A',
                        'client_id' => $project->client_id,
                        'location' => $project->location ?? 'N/A',
                        'category' => $categoryName,
                        'status' => ProjectStatus::STATUS_IN_PROGRESS,
                    ];
                });

            $inProgressItems = array_merge($inProgressItems, $items->toArray());
        }

        // Apply filters if any statuses are selected
        if (!empty($selectedStatuses)) {
            // Filter logic can be added here if needed
        }

        // Get all available statuses for filter checkboxes
        $allStatuses = [
            'Pending GITA Application',
            'Pending Meter Change',
            'Pending NEM Quota Approval',
            'Pending NEM Quota Submission',
            'Pending NEM Welcome Letter',
            'Pending Site Installation',
            'Pending ST License Application',
            'Pending ST License Approval',
        ];

        return view('status-summary', compact('summary', 'inProgressItems', 'allStatuses', 'selectedStatuses'));
    }
}

