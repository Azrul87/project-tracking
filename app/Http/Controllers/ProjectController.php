<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of all projects.
     */
    public function index(Request $request)
    {
        $query = Project::with(['client', 'salesPic']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('project_id', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('client', function($clientQuery) use ($search) {
                      $clientQuery->where('client_name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Installer filter
        if ($request->has('installer') && $request->installer) {
            if ($request->installer === 'other') {
                $query->where('installer', 'Other');
            } else {
                $query->where('installer', $request->installer);
            }
        }

        // Payment Status filter
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $projects = $query->latest()->get();

        // Get unique values for filter dropdowns
        $statuses = Project::distinct()->whereNotNull('status')->pluck('status')->sort()->values();
        $categories = Project::distinct()->whereNotNull('category')->pluck('category')->sort()->values();
        $installers = Project::distinct()->whereNotNull('installer')->where('installer', '!=', 'Other')->pluck('installer')->sort()->values();
        $paymentStatuses = Project::distinct()->whereNotNull('payment_status')->pluck('payment_status')->sort()->values();
        $hasOtherInstaller = Project::where('installer', 'Other')->exists();

        return view('projects', compact('projects', 'statuses', 'categories', 'installers', 'paymentStatuses', 'hasOtherInstaller'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        $clients = Client::orderBy('client_name')->get();
        $users = User::orderBy('name')->get();
        return view('projects.create', compact('clients', 'users'));
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
            $validated = $request->validate([
                'client_id' => 'required|string|exists:clients,client_id',
                'sales_pic_id' => 'required|string|exists:users,user_id',
                'name' => 'nullable|string|max:255',
                'category' => 'nullable|string|max:255',
                'scheme' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'pv_system_capacity_kwp' => 'nullable|numeric|min:0',
                'ev_charger_capacity' => 'nullable|string|max:255',
                'bess_capacity' => 'nullable|string|max:255',
                'project_value_rm' => 'nullable|numeric|min:0',
                'vo_rm' => 'nullable|numeric|min:0',
                'payment_method' => 'nullable|string|max:255',
                'contract_type' => 'nullable|string|max:255',
                'insurance_warranty' => 'nullable|string|max:255',
                'dlp_period' => 'nullable|string|max:255',
                'om_details' => 'nullable|string',
                'partner' => 'nullable|string|max:255',
                'services_exclusion' => 'nullable|string',
                'additional_remark' => 'nullable|string',
                'closed_date' => 'nullable|date',
                'status' => 'nullable|string|max:255',
                'invoice_status' => 'nullable|string|max:255',
                'payment_status' => 'nullable|string|max:255',
                'procurement_status' => 'nullable|string|max:255',
                'module' => 'nullable|string|max:255',
                'module_quantity' => 'nullable|integer|min:0',
                'inverter' => 'nullable|string|max:255',
                'project_status' => 'nullable|string|max:255',
                'site_survey_date' => 'nullable|date',
                'installer' => 'nullable|string|max:255',
                'installer_other' => 'nullable|string|max:255',
                'installation_date' => 'nullable|date',
                // EPCC Workflow Dates
                'client_enquiry_date' => 'nullable|date',
                'proposal_preparation_date' => 'nullable|date',
                'proposal_submission_date' => 'nullable|date',
                'proposal_acceptance_date' => 'nullable|date',
                'letter_of_award_date' => 'nullable|date',
                'first_invoice_date' => 'nullable|date',
                'first_invoice_payment_date' => 'nullable|date',
                'site_study_date' => 'nullable|date',
                'nem_application_submission_date' => 'nullable|date',
                'project_planning_date' => 'nullable|date',
                'nem_approval_date' => 'nullable|date',
                'st_license_application_date' => 'nullable|date',
                'second_invoice_date' => 'nullable|date',
                'second_invoice_payment_date' => 'nullable|date',
                'material_procurement_date' => 'nullable|date',
                'subcon_appointment_date' => 'nullable|date',
                'material_delivery_date' => 'nullable|date',
                'site_mobilization_date' => 'nullable|date',
                'st_license_approval_date' => 'nullable|date',
                'system_testing_date' => 'nullable|date',
                'system_commissioning_date' => 'nullable|date',
                'nem_meter_change_date' => 'nullable|date',
                'last_invoice_date' => 'nullable|date',
                'last_invoice_payment_date' => 'nullable|date',
                'system_energize_date' => 'nullable|date',
                'nemcd_obtained_date' => 'nullable|date',
                'system_training_date' => 'nullable|date',
                'project_handover_to_client_date' => 'nullable|date',
                'project_closure_date' => 'nullable|date',
                'handover_to_om_date' => 'nullable|date',
                // O&M Workflow Dates
                'om_site_study_date' => 'nullable|date',
                'om_schedule_prepared_date' => 'nullable|date',
                'om_start_date' => 'nullable|date',
                'om_end_date' => 'nullable|date',
                'om_status' => 'nullable|string|max:255',
            ]);

        // Auto-generate project ID
        $validated['project_id'] = Project::generateProjectId();
        
        // Set initial workflow stage to "Client Enquiry" (BDT starts here)
        $validated['workflow_stage'] = Project::WORKFLOW_CLIENT_ENQUIRY;
        $validated['status'] = $validated['status'] ?? 'Planning';
        
        // Set client enquiry date to today if not provided
        if (empty($validated['client_enquiry_date'])) {
            $validated['client_enquiry_date'] = now()->toDateString();
        }

        $project = Project::create($validated);
        
        // Create initial workflow status record
        $project->setStatus(
            \App\Models\ProjectStatus::TYPE_CLIENT_ENQUIRY,
            \App\Models\ProjectStatus::STATUS_IN_PROGRESS,
            Auth::check() ? Auth::user()->user_id : null,
            'Project created by sales person (BDT) - Client Enquiry stage initiated'
        );

        return redirect()->route('projects.index')->with('success', 'Project created successfully. Workflow stage set to: Client Enquiry');
    }

    /**
     * Display the specified project.
     */
    public function show($id)
    {
        // Sample project data - in a real application, this would come from a database
        $projects = [
            1 => [
                'id' => 1,
                'project_no' => '#PRJ-001',
                'client' => 'ABC Corporation',
                'location' => 'New York, NY',
                'status' => 'active',
                'payment_status' => 'paid',
                'installer' => 'John Doe',
                'installation_date' => '2024-01-15',
                'sales_pic' => 'Sarah Wilson',
                'category' => 'commercial'
            ],
            2 => [
                'id' => 2,
                'project_no' => '#PRJ-002',
                'client' => 'XYZ Industries',
                'location' => 'Los Angeles, CA',
                'status' => 'pending',
                'payment_status' => 'pending',
                'installer' => 'Jane Smith',
                'installation_date' => '2024-02-20',
                'sales_pic' => 'Mike Johnson',
                'category' => 'industrial'
            ],
            3 => [
                'id' => 3,
                'project_no' => '#PRJ-003',
                'client' => 'Tech Solutions Ltd',
                'location' => 'Chicago, IL',
                'status' => 'completed',
                'payment_status' => 'paid',
                'installer' => 'Mike Johnson',
                'installation_date' => '2024-01-10',
                'sales_pic' => 'John Doe',
                'category' => 'residential'
            ],
            4 => [
                'id' => 4,
                'project_no' => '#PRJ-004',
                'client' => 'Global Enterprises',
                'location' => 'Miami, FL',
                'status' => 'on-hold',
                'payment_status' => 'overdue',
                'installer' => 'Sarah Wilson',
                'installation_date' => '2024-03-05',
                'sales_pic' => 'Jane Smith',
                'category' => 'commercial'
            ],
            5 => [
                'id' => 5,
                'project_no' => '#PRJ-005',
                'client' => 'Innovation Corp',
                'location' => 'Seattle, WA',
                'status' => 'active',
                'payment_status' => 'pending',
                'installer' => 'John Doe',
                'installation_date' => '2024-02-28',
                'sales_pic' => 'Sarah Wilson',
                'category' => 'renovation'
            ]
        ];

        $project = $projects[$id] ?? $projects[1]; // Default to first project if ID not found
        
        return view('project-detail', compact('project'));
    }

    /**
     * Display the per-project dashboard view with workflow progress.
     */
    public function dashboard($id)
    {
        $project = Project::with(['client', 'salesPic', 'files', 'statuses'])->find($id);

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found.');
        }

        // Get workflow stages
        $epccStages = \App\Helpers\WorkflowHelper::getEPCCWorkflowStages();
        $omStages = \App\Helpers\WorkflowHelper::getOMWorkflowStages();
        
        // Get current workflow stage
        $currentStage = $project->workflow_stage ?? Project::WORKFLOW_CLIENT_ENQUIRY;
        
        // Process EPCC stages with status
        $workflowProgress = [];
        $completedCount = 0;
        $totalStages = count($epccStages);
        $stageKeys = array_keys($epccStages);
        $currentStageIndex = array_search($currentStage, $stageKeys);
        if ($currentStageIndex === false) {
            $currentStageIndex = 0;
        }
        
        foreach ($epccStages as $stageKey => $stageInfo) {
            $dateField = $stageInfo['date_field'];
            $dateValue = $project->$dateField ?? null;
            
            // Determine stage status
            $status = 'pending';
            $stageIndex = array_search($stageKey, $stageKeys);
            
            if ($dateValue) {
                $status = 'completed';
                $completedCount++;
            } elseif ($stageKey === $currentStage) {
                $status = 'in_progress';
            } elseif ($stageIndex < $currentStageIndex) {
                // Stage is before current stage but no date - might be skipped or not tracked
                $status = 'pending';
            }
            
            // Get status from ProjectStatus table
            $statusRecord = $project->getLatestStatus($stageInfo['status_type']);
            $statusValue = $statusRecord ? $statusRecord->status_value : null;
            
            $workflowProgress[] = [
                'key' => $stageKey,
                'label' => $stageInfo['label'],
                'responsible' => $stageInfo['responsible'],
                'description' => $stageInfo['description'],
                'status' => $status,
                'status_value' => $statusValue,
                'date' => $dateValue,
                'date_field' => $dateField,
                'is_current' => $stageKey === $currentStage,
            ];
        }
        
        // Calculate progress percentage
        $progressPercentage = $totalStages > 0 ? round(($completedCount / $totalStages) * 100) : 0;
        
        // Process O&M stages
        $omProgress = [];
        foreach ($omStages as $stageKey => $stageInfo) {
            $dateField = $stageInfo['date_field'];
            $dateValue = $dateField ? ($project->$dateField ?? null) : null;
            
            $status = 'pending';
            if ($dateValue) {
                $status = 'completed';
            }
            
            $omProgress[] = [
                'key' => $stageKey,
                'label' => $stageInfo['label'],
                'responsible' => $stageInfo['responsible'],
                'description' => $stageInfo['description'],
                'status' => $status,
                'date' => $dateValue,
            ];
        }

        $projectFiles = $project->files()->latest()->get();

        return view('project-workflow', compact('project', 'workflowProgress', 'omProgress', 'currentStage', 'progressPercentage', 'projectFiles'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project)
    {
        $clients = Client::orderBy('client_name')->get();
        $users = User::orderBy('name')->get();
        return view('projects.edit', compact('project', 'clients', 'users'));
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Project $project)
    {
        try {
            // Log the update attempt for debugging
            Log::info('Updating project', ['project_id' => $project->project_id, 'request_method' => $request->method()]);

            // Ensure we have a valid project instance
            if (!$project || !$project->exists) {
                Log::error('Project not found during update', ['project_id' => $project->project_id ?? 'unknown']);
                abort(404, 'Project not found');
            }

            // Store the original project_id to ensure it doesn't change
            $originalProjectId = $project->project_id;

            $validated = $request->validate([
                'client_id' => 'required|string|exists:clients,client_id',
                'sales_pic_id' => 'required|string|exists:users,user_id',
                'name' => 'nullable|string|max:255',
                'category' => 'nullable|string|max:255',
                'scheme' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'pv_system_capacity_kwp' => 'nullable|numeric|min:0',
                'ev_charger_capacity' => 'nullable|string|max:255',
                'bess_capacity' => 'nullable|string|max:255',
                'project_value_rm' => 'nullable|numeric|min:0',
                'vo_rm' => 'nullable|numeric|min:0',
                'payment_method' => 'nullable|string|max:255',
                'contract_type' => 'nullable|string|max:255',
                'insurance_warranty' => 'nullable|string|max:255',
                'dlp_period' => 'nullable|string|max:255',
                'om_details' => 'nullable|string',
                'partner' => 'nullable|string|max:255',
                'services_exclusion' => 'nullable|string',
                'additional_remark' => 'nullable|string',
                'closed_date' => 'nullable|date',
                'status' => 'nullable|string|max:255',
                'invoice_status' => 'nullable|string|max:255',
                'payment_status' => 'nullable|string|max:255',
                'procurement_status' => 'nullable|string|max:255',
                'module' => 'nullable|string|max:255',
                'module_quantity' => 'nullable|integer|min:0',
                'inverter' => 'nullable|string|max:255',
                'project_status' => 'nullable|string|max:255',
                'site_survey_date' => 'nullable|date',
                'installer' => 'nullable|string|max:255',
                'installer_other' => 'nullable|string|max:255',
                'installation_date' => 'nullable|date',
                // Workflow stage
                'workflow_stage' => 'nullable|string|max:255',
                // EPCC Workflow Dates
                'client_enquiry_date' => 'nullable|date',
                'proposal_preparation_date' => 'nullable|date',
                'proposal_submission_date' => 'nullable|date',
                'proposal_acceptance_date' => 'nullable|date',
                'letter_of_award_date' => 'nullable|date',
                'first_invoice_date' => 'nullable|date',
                'first_invoice_payment_date' => 'nullable|date',
                'site_study_date' => 'nullable|date',
                'nem_application_submission_date' => 'nullable|date',
                'project_planning_date' => 'nullable|date',
                'nem_approval_date' => 'nullable|date',
                'st_license_application_date' => 'nullable|date',
                'second_invoice_date' => 'nullable|date',
                'second_invoice_payment_date' => 'nullable|date',
                'material_procurement_date' => 'nullable|date',
                'subcon_appointment_date' => 'nullable|date',
                'material_delivery_date' => 'nullable|date',
                'site_mobilization_date' => 'nullable|date',
                'st_license_approval_date' => 'nullable|date',
                'system_testing_date' => 'nullable|date',
                'system_commissioning_date' => 'nullable|date',
                'nem_meter_change_date' => 'nullable|date',
                'last_invoice_date' => 'nullable|date',
                'last_invoice_payment_date' => 'nullable|date',
                'system_energize_date' => 'nullable|date',
                'nemcd_obtained_date' => 'nullable|date',
                'system_training_date' => 'nullable|date',
                'project_handover_to_client_date' => 'nullable|date',
                'project_closure_date' => 'nullable|date',
                'handover_to_om_date' => 'nullable|date',
                // O&M Workflow Dates
                'om_site_study_date' => 'nullable|date',
                'om_schedule_prepared_date' => 'nullable|date',
                'om_start_date' => 'nullable|date',
                'om_end_date' => 'nullable|date',
                'om_status' => 'nullable|string|max:255',
            ]);

            // Convert empty strings to null for nullable fields
            $validated = array_map(function ($value) {
                return $value === '' ? null : $value;
            }, $validated);

            // Use database transaction to ensure data integrity
            DB::transaction(function () use ($project, $validated, $originalProjectId) {
                // Refresh the project to ensure we have the latest data
                $project->refresh();

                // Verify project still exists before updating
                if (!$project->exists) {
                    throw new \Exception('Project was deleted during update process');
                }

                // Update workflow stage status if dates are provided
                $epccStages = \App\Helpers\WorkflowHelper::getEPCCWorkflowStages();
                foreach ($epccStages as $stageKey => $stageInfo) {
                    $dateField = $stageInfo['date_field'];
                    if (isset($validated[$dateField]) && $validated[$dateField]) {
                        // Update status to completed when date is set
                        $project->setStatus(
                            $stageInfo['status_type'],
                            \App\Models\ProjectStatus::STATUS_COMPLETED,
                            Auth::check() ? Auth::user()->user_id : null,
                            "Stage completed on {$validated[$dateField]}"
                        );
                    }
                }

                // Update the project (explicitly exclude project_id from being updated)
                $project->fill($validated);
                $project->project_id = $originalProjectId; // Ensure project_id never changes
                $project->save();

                // Verify project still exists after update
                $project->refresh();
                if (!$project->exists) {
                    throw new \Exception('Project was deleted after update');
                }
            });

            Log::info('Project updated successfully', ['project_id' => $project->project_id]);

            return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during project update', ['project_id' => $project->project_id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating project', ['project_id' => $project->project_id ?? 'unknown', 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update project: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }
}
