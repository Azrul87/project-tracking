<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Item;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with summary of all projects
     */
    public function index(Request $request)
    {
        // Get all projects with relationships
        $projects = Project::with(['client', 'salesPic', 'files', 'payments', 'workflowStage'])->get();
        
        // Overall Statistics
        $totalProjects = $projects->count();
        $projectsByStatus = $projects->groupBy('status')->map->count();
        
        // Calculate overall financial statistics
        $totalContractValue = $projects->sum(function($p) {
            return ($p->project_value_rm ?? 0) + ($p->vo_rm ?? 0);
        });
        
        $totalInvoiced = Payment::sum('invoice_amount') ?? 0;
        $totalPaid = Payment::sum('payment_amount') ?? 0;
        $totalOutstanding = max(0, $totalContractValue - $totalPaid);
        
        // Calculate overall progress across all projects
        $workflowStages = [
            'client_enquiry_date', 'proposal_preparation_date', 'proposal_submission_date',
            'proposal_acceptance_date', 'letter_of_award_date', 'first_invoice_date',
            'first_invoice_payment_date', 'site_study_date', 'nem_application_submission_date',
            'project_planning_date', 'nem_approval_date', 'st_license_application_date',
            'second_invoice_date', 'second_invoice_payment_date', 'material_procurement_date',
            'subcon_appointment_date', 'material_delivery_date', 'site_mobilization_date',
            'st_license_approval_date', 'system_testing_date', 'system_commissioning_date',
            'nem_meter_change_date', 'last_invoice_date', 'last_invoice_payment_date',
            'system_energize_date', 'nemcd_obtained_date', 'system_training_date',
            'project_handover_to_client_date', 'project_closure_date',
        ];
        
        $totalStages = count($workflowStages);
        $totalPossibleStages = $totalProjects * $totalStages;
        $totalCompletedStages = 0;
        
        foreach ($projects as $project) {
            foreach ($workflowStages as $stage) {
                if ($project->$stage) {
                    $totalCompletedStages++;
                }
            }
        }
        
        $overallWorkflowProgress = $totalPossibleStages > 0 ? ($totalCompletedStages / $totalPossibleStages) * 100 : 0;
        $overallPaymentProgress = $totalContractValue > 0 ? ($totalPaid / $totalContractValue) * 100 : 0;
        $overallProgress = ($overallWorkflowProgress * 0.4) + ($overallPaymentProgress * 0.6);
        
        // Calculate task status distribution across all projects
        $taskStatus = $this->calculateOverallTaskStatus($projects, $workflowStages);
        
        // Calculate weekly progress (aggregate across all projects)
        $weeklyProgress = $this->calculateOverallWeeklyProgress($projects, $workflowStages);
        
        // Get recent projects for timeline
        $recentProjects = $projects->sortByDesc('created_at')->take(10);
        
        // Get total files count
        $totalFiles = ProjectFile::count();
        
        // Get total clients
        $totalClients = Client::count();
        
        // Get total materials
        $totalMaterials = Item::count();
        
        // Calculate average project duration
        $completedProjects = $projects->filter(function($p) {
            return $p->client_enquiry_date && $p->project_closure_date;
        });
        
        $avgDuration = 0;
        if ($completedProjects->count() > 0) {
            $totalDays = $completedProjects->sum(function($p) {
                $start = Carbon::parse($p->client_enquiry_date);
                $end = Carbon::parse($p->project_closure_date);
                return $start->diffInDays($end);
            });
            $avgDuration = round($totalDays / $completedProjects->count());
        }
        
        // Prepare stats
        $stats = [
            'total_projects' => $totalProjects,
            'projects_planning' => $projectsByStatus['Planning'] ?? 0,
            'projects_in_progress' => $projectsByStatus['In Progress'] ?? 0,
            'projects_completed' => $projectsByStatus['Completed'] ?? 0,
            'total_contract_value' => $totalContractValue,
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'total_outstanding' => $totalOutstanding,
            'overall_progress' => round($overallProgress, 1),
            'workflow_progress' => round($overallWorkflowProgress, 1),
            'payment_progress' => round($overallPaymentProgress, 1),
            'total_completed_stages' => $totalCompletedStages,
            'total_possible_stages' => $totalPossibleStages,
            'total_files' => $totalFiles,
            'total_clients' => $totalClients,
            'total_materials' => $totalMaterials,
            'avg_duration' => $avgDuration,
        ];
        
        // Prepare chart data
        $chartData = [
            'weekly_progress' => $weeklyProgress,
            'task_status' => $taskStatus,
        ];
        
        return view('dashboard', compact('projects', 'stats', 'chartData', 'recentProjects'));
    }
    
    /**
     * Calculate overall weekly progress across all projects
     */
    private function calculateOverallWeeklyProgress($projects, $workflowStages)
    {
        $weeks = [];
        $now = Carbon::now();
        
        for ($i = 3; $i >= 0; $i--) {
            $weekStart = $now->copy()->subWeeks($i)->startOfWeek();
            $weekEnd = $weekStart->copy()->endOfWeek();
            
            // Count completed stages across all projects in this week
            $stagesCompleted = 0;
            $totalStagesInWeek = 0;
            
            foreach ($projects as $project) {
                foreach ($workflowStages as $stage) {
                    if ($project->$stage) {
                        $stageDate = Carbon::parse($project->$stage);
                        if ($stageDate->between($weekStart, $weekEnd)) {
                            $stagesCompleted++;
                        }
                    }
                    $totalStagesInWeek++;
                }
            }
            
            // Calculate progress percentage for this week
            $progress = $totalStagesInWeek > 0 ? (($stagesCompleted / $totalStagesInWeek) * 100) : 0;
            
            $weeks[] = [
                'label' => 'Week ' . (4 - $i),
                'planned' => min(100, 15 + ($i * 15)), // Simulated planned progress
                'actual' => min(100, $progress), // Actual progress
            ];
        }
        
        return $weeks;
    }
    
    /**
     * Calculate overall task status distribution across all projects
     */
    private function calculateOverallTaskStatus($projects, $workflowStages)
    {
        $completed = 0;
        $inProgress = 0;
        $atRisk = 0;
        $delayed = 0;
        $now = Carbon::now();
        
        foreach ($projects as $project) {
            foreach ($workflowStages as $stage) {
                if ($project->$stage) {
                    $completed++;
                } else {
                    // Check if stage is overdue
                    $estimatedDate = $this->getEstimatedDateForStage($project, $stage);
                    if ($estimatedDate && $estimatedDate->isPast()) {
                        $delayed++;
                    } else {
                        $nextStage = $this->getNextExpectedStage($project);
                        if ($stage === $nextStage) {
                            $inProgress++;
                        } else {
                            $atRisk++;
                        }
                    }
                }
            }
        }
        
        return [
            'completed' => $completed,
            'in_progress' => $inProgress,
            'at_risk' => $atRisk,
            'delayed' => $delayed,
        ];
    }
    
    /**
     * Get estimated date for a stage
     */
    private function getEstimatedDateForStage($project, $stage)
    {
        $startDate = $project->client_enquiry_date ?? $project->created_at;
        if (!$startDate) {
            return null;
        }
        
        $stageOrder = [
            'client_enquiry_date' => 0,
            'proposal_preparation_date' => 7,
            'proposal_submission_date' => 14,
            'proposal_acceptance_date' => 21,
            'letter_of_award_date' => 28,
            'first_invoice_date' => 35,
            'first_invoice_payment_date' => 42,
            'site_study_date' => 49,
            'nem_application_submission_date' => 56,
            'project_planning_date' => 63,
            'nem_approval_date' => 70,
            'st_license_application_date' => 77,
            'second_invoice_date' => 84,
            'second_invoice_payment_date' => 91,
            'material_procurement_date' => 98,
            'subcon_appointment_date' => 105,
            'material_delivery_date' => 112,
            'site_mobilization_date' => 119,
            'st_license_approval_date' => 126,
            'system_testing_date' => 133,
            'system_commissioning_date' => 140,
            'nem_meter_change_date' => 147,
            'last_invoice_date' => 154,
            'last_invoice_payment_date' => 161,
            'system_energize_date' => 168,
            'nemcd_obtained_date' => 175,
            'system_training_date' => 182,
            'project_handover_to_client_date' => 189,
            'project_closure_date' => 196,
        ];
        
        $daysOffset = $stageOrder[$stage] ?? null;
        if ($daysOffset === null) {
            return null;
        }
        
        return Carbon::parse($startDate)->addDays($daysOffset);
    }
    
    /**
     * Get next expected stage
     */
    private function getNextExpectedStage($project)
    {
        $stages = [
            'client_enquiry_date',
            'proposal_preparation_date',
            'proposal_submission_date',
            'proposal_acceptance_date',
            'letter_of_award_date',
            'first_invoice_date',
            'first_invoice_payment_date',
            'site_study_date',
            'nem_application_submission_date',
            'project_planning_date',
            'nem_approval_date',
            'st_license_application_date',
            'second_invoice_date',
            'second_invoice_payment_date',
            'material_procurement_date',
            'subcon_appointment_date',
            'material_delivery_date',
            'site_mobilization_date',
            'st_license_approval_date',
            'system_testing_date',
            'system_commissioning_date',
            'nem_meter_change_date',
            'last_invoice_date',
            'last_invoice_payment_date',
            'system_energize_date',
            'nemcd_obtained_date',
            'system_training_date',
            'project_handover_to_client_date',
            'project_closure_date',
        ];
        
        foreach ($stages as $stage) {
            if (!$project->$stage) {
                return $stage;
            }
        }
        
        return null;
    }
}
