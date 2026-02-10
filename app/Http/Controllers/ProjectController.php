<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Client;
use App\Models\User;
use App\Models\ProjectFile;
use App\Models\ProjectWorkflowStage;
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
        $query = Project::with(['client', 'salesPic', 'workflowStage']);

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
        // Payment status is now calculated from payments table, so filtering is done in the view

        $projects = $query->latest()->get();

        // Get unique values for filter dropdowns
        $statuses = Project::distinct()->whereNotNull('status')->pluck('status')->sort()->values();
        $categories = Project::distinct()->whereNotNull('category')->pluck('category')->sort()->values();
        $installers = Project::distinct()->whereNotNull('installer')->where('installer', '!=', 'Other')->pluck('installer')->sort()->values();
        // Payment statuses are now calculated from payments table
        $paymentStatuses = collect(['Pending 1st Payment', 'Fully Paid', 'Overdue', '1 Payment(s) Received', '2 Payment(s) Received', '3 Payment(s) Received']);
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
        $workflowStages = \App\Helpers\WorkflowHelper::getEPCCWorkflowStages();
        return view('projects.create', compact('clients', 'users', 'workflowStages'));
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
        $validated['status'] = $validated['status'] ?? 'Planning';
        
        // Extract workflow fields from validated data
        $workflowData = [];
        $workflowFields = [
            'client_enquiry_date', 'proposal_preparation_date', 'proposal_submission_date',
            'proposal_acceptance_date', 'letter_of_award_date', 'first_invoice_date',
            'first_invoice_payment_date', 'site_study_date', 'nem_application_submission_date',
            'project_planning_date', 'nem_approval_date', 'st_license_application_date',
            'second_invoice_date', 'second_invoice_payment_date', 'material_procurement_date',
            'subcon_appointment_date', 'material_delivery_date', 'site_mobilization_date',
            'st_license_approval_date', 'system_testing_date', 'system_commissioning_date',
            'nem_meter_change_date', 'last_invoice_date', 'last_invoice_payment_date',
            'system_energize_date', 'nemcd_obtained_date', 'system_training_date',
            'project_handover_to_client_date', 'project_closure_date', 'handover_to_om_date',
            'om_site_study_date', 'om_schedule_prepared_date', 'om_start_date', 'om_end_date',
            'workflow_stage', 'om_status',
        ];
        
        foreach ($workflowFields as $field) {
            if (isset($validated[$field])) {
                $workflowData[$field] = $validated[$field];
                unset($validated[$field]);
            }
        }
        
        // Set initial workflow stage to "Client Enquiry" (BDT starts here)
        $workflowData['workflow_stage'] = $workflowData['workflow_stage'] ?? Project::WORKFLOW_CLIENT_ENQUIRY;
        
        // Set client enquiry date to today if not provided
        if (empty($workflowData['client_enquiry_date'])) {
            $workflowData['client_enquiry_date'] = now()->toDateString();
        }

        // Use transaction to ensure data consistency
        DB::beginTransaction();
        try {
            // Create the project - boot() method will auto-create workflow stage
            $project = Project::create($validated);
            
            // Update the auto-created workflow stage with our workflow data
            ProjectWorkflowStage::where('project_id', $project->project_id)
                ->update($workflowData);
            
            // Refresh to load the updated relationship
            $project->load('workflowStage');
            
            // Create initial workflow status record
            $project->setStatus(
                \App\Models\ProjectStatus::TYPE_CLIENT_ENQUIRY,
                \App\Models\ProjectStatus::STATUS_IN_PROGRESS,
                Auth::check() ? Auth::user()->user_id : null,
                'Project created by sales person (BDT) - Client Enquiry stage initiated'
            );
            
            DB::commit();
            
            return redirect()->route('projects.index')->with('success', 'Project created successfully. Workflow stage set to: Client Enquiry');
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            Log::error('Database error creating project', ['error' => $e->getMessage()]);
            
            $errorMessage = $this->translateDatabaseError($e);
            return redirect()->back()->withInput()->withErrors(['error' => $errorMessage]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating project', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->withErrors(['error' => 'An unexpected error occurred while creating the project. Please try again or contact support if the problem persists.']);
        }
    }



    /**
     * Show the form for editing project materials.
     */
    public function editMaterials(Project $project)
    {
        // Load materials relationship
        $project->load('materials');
        
        // Load material fields dynamically from database
        $materialFields = \App\Models\Material::orderBy('category')
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();

        return view('projects.edit-materials', compact('project', 'materialFields'));
    }

    /**
     * Update the specified project materials in storage.
     */
    public function updateMaterials(Request $request, Project $project)
    {
        // Get all materials from database
        $materials = \App\Models\Material::pluck('id', 'code');
        
        // Build validation rules dynamically
        $rules = [];
        foreach ($materials->keys() as $code) {
            $rules[$code] = 'nullable|integer|min:0';
        }
        
        $validated = $request->validate($rules);

        // Update materials - delete all existing and insert new ones
        // This is cleaner than updating each one individually
        $project->projectMaterials()->delete();
        
        foreach ($validated as $materialCode => $quantity) {
            if ($quantity > 0 && isset($materials[$materialCode])) {
                \App\Models\ProjectMaterial::create([
                    'project_id' => $project->project_id,
                    'material_id' => $materials[$materialCode],
                    'quantity' => $quantity,
                ]);
            }
        }

        return redirect()->route('inventory')->with('success', 'Materials updated successfully');
    }

    public function show($id)
    {
        // Get project with related data
        // Try to find by ID (numeric) or project_id (string)
        $project = Project::with(['client', 'salesPic', 'materials'])
            ->where('id', $id)
            ->orWhere('project_id', $id)
            ->firstOrFail();
            
        return view('project-detail', compact('project'));
    }

    /**
     * Display the per-project dashboard view with workflow progress.
     */
    public function dashboard($id)
    {
        // Always get fresh data from database, don't use cached relationships
        $project = Project::with(['client', 'salesPic', 'files.uploader', 'statuses'])->find($id);

        if (!$project) {
            return redirect()->route('projects.index')->with('error', 'Project not found.');
        }
        
        // Force reload workflowStage from database to get latest data
        $project->unsetRelation('workflowStage');
        $project->load('workflowStage');

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
        
        // Get workflow stage directly from database to ensure fresh data
        $workflowStage = ProjectWorkflowStage::where('project_id', $project->project_id)->first();
        
        foreach ($epccStages as $stageKey => $stageInfo) {
            $dateField = $stageInfo['date_field'];
            
            // Get the date value directly from workflow stage model
            $dateValue = $workflowStage ? ($workflowStage->$dateField ?? null) : null;
            
            // Determine stage status
            $status = 'pending';
            $stageIndex = array_search($stageKey, $stageKeys);
            
            // Check if date exists - handle Carbon objects, DateTime, and date strings
            $hasDate = !empty($dateValue) && (
                $dateValue instanceof \Carbon\Carbon || 
                $dateValue instanceof \DateTime || 
                (is_string($dateValue) && strlen(trim($dateValue)) > 0)
            );
            
            if ($hasDate) {
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
            // Get date directly from workflow stage model
            $dateValue = $workflowStage && $dateField ? ($workflowStage->$dateField ?? null) : null;
            
            $status = 'pending';
            // Check if date exists
            $hasDate = !empty($dateValue) && (
                $dateValue instanceof \Carbon\Carbon || 
                $dateValue instanceof \DateTime || 
                (is_string($dateValue) && strlen(trim($dateValue)) > 0)
            );
            
            if ($hasDate) {
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
        // Eager load the workflowStage relationship
        $project->load('workflowStage');
        
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

            // Extract workflow fields from validated data
            $workflowData = [];
            $workflowFields = [
                'client_enquiry_date', 'proposal_preparation_date', 'proposal_submission_date',
                'proposal_acceptance_date', 'letter_of_award_date', 'first_invoice_date',
                'first_invoice_payment_date', 'site_study_date', 'nem_application_submission_date',
                'project_planning_date', 'nem_approval_date', 'st_license_application_date',
                'second_invoice_date', 'second_invoice_payment_date', 'material_procurement_date',
                'subcon_appointment_date', 'material_delivery_date', 'site_mobilization_date',
                'st_license_approval_date', 'system_testing_date', 'system_commissioning_date',
                'nem_meter_change_date', 'last_invoice_date', 'last_invoice_payment_date',
                'system_energize_date', 'nemcd_obtained_date', 'system_training_date',
                'project_handover_to_client_date', 'project_closure_date', 'handover_to_om_date',
                'om_site_study_date', 'om_schedule_prepared_date', 'om_start_date', 'om_end_date',
                'workflow_stage', 'om_status',
            ];
            
            foreach ($workflowFields as $field) {
                if (isset($validated[$field])) {
                    $workflowData[$field] = $validated[$field];
                    unset($validated[$field]);
                }
            }

            // Use database transaction to ensure data integrity
            DB::transaction(function () use ($project, $validated, $workflowData, $originalProjectId) {
                // Refresh the project to ensure we have the latest data
                $project->refresh();

                // Verify project still exists before updating
                if (!$project->exists) {
                    throw new \Exception('Project was deleted during update process');
                }

                // Update or create workflow stage record
                if (!empty($workflowData)) {
                    ProjectWorkflowStage::updateOrCreate(
                        ['project_id' => $project->project_id],
                        $workflowData
                    );
                }

                // Update workflow stage status if dates are provided
                $epccStages = \App\Helpers\WorkflowHelper::getEPCCWorkflowStages();
                foreach ($epccStages as $stageKey => $stageInfo) {
                    $dateField = $stageInfo['date_field'];
                    if (isset($workflowData[$dateField]) && $workflowData[$dateField]) {
                        // Update status to completed when date is set
                        $project->setStatus(
                            $stageInfo['status_type'],
                            \App\Models\ProjectStatus::STATUS_COMPLETED,
                            Auth::check() ? Auth::user()->user_id : null,
                            "Stage completed on {$workflowData[$dateField]}"
                        );
                    }
                }

                // Update the project (explicitly exclude project_id from being updated)
                $project->fill($validated);
                $project->project_id = $originalProjectId; // Ensure project_id never changes
                $project->save();

                // Clear all relationship caches to ensure fresh data
                $project->unsetRelation('workflowStage');
                $project->unsetRelation('statuses');
                
                // Verify project still exists after update
                $project->refresh();
                if (!$project->exists) {
                    throw new \Exception('Project was deleted after update');
                }
                
                // Reload relationships with fresh data
                $project->load(['workflowStage', 'statuses']);
            });

            Log::info('Project updated successfully', ['project_id' => $project->project_id]);

            return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Validation failed during project update', ['project_id' => $project->project_id, 'errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database-specific errors with user-friendly messages
            Log::error('Database error updating project', ['project_id' => $project->project_id ?? 'unknown', 'error' => $e->getMessage()]);
            
            $errorMessage = $this->translateDatabaseError($e);
            return redirect()->back()->withInput()->withErrors(['error' => $errorMessage]);
        } catch (\Exception $e) {
            Log::error('Error updating project', ['project_id' => $project->project_id ?? 'unknown', 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->withInput()->withErrors(['error' => 'An unexpected error occurred while updating the project. Please try again or contact support if the problem persists.']);
        }
    }

    /**
     * Translate database errors into user-friendly messages.
     */
    private function translateDatabaseError(\Illuminate\Database\QueryException $e): string
    {
        $errorMessage = $e->getMessage();
        $errorCode = $e->getCode();
        
        // Map database column names to user-friendly field names
        $fieldNames = [
            'pv_system_capacity_kwp' => 'PV System Capacity',
            'project_value_rm' => 'Project Value',
            'vo_rm' => 'VO Amount',
            'module_quantity' => 'Module Quantity',
            'client_id' => 'Client',
            'sales_pic_id' => 'Sales PIC',
            'name' => 'Project Name',
            'location' => 'Location',
            'payment_method' => 'Payment Method',
            'contract_type' => 'Contract Type',
            'insurance_warranty' => 'Insurance/Warranty',
            'project_status' => 'Project Status',
            'installer' => 'Installer',
        ];
        
        // Numeric value out of range error (SQLSTATE 22003)
        if (strpos($errorMessage, '22003') !== false || strpos($errorMessage, 'Out of range') !== false) {
            // Extract the column name from the error message
            preg_match("/column '([^']+)'/", $errorMessage, $matches);
            $columnName = $matches[1] ?? 'unknown field';
            $friendlyName = $fieldNames[$columnName] ?? $columnName;
            
            // Extract the value that caused the error
            preg_match("/`$columnName`\s*=\s*([0-9.]+)/", $errorMessage, $valueMatches);
            $badValue = $valueMatches[1] ?? 'the entered value';
            
            return "The value '$badValue' is too large for the '$friendlyName' field. Please enter a smaller number. Maximum allowed values are typically under 1 million for currency fields and under 100,000 for capacity fields.";
        }
        
        // Duplicate entry error (SQLSTATE 23000)
        if (strpos($errorMessage, '23000') !== false || strpos($errorMessage, 'Duplicate entry') !== false) {
            preg_match("/Duplicate entry '([^']+)' for key '([^']+)'/", $errorMessage, $matches);
            $duplicateValue = $matches[1] ?? 'this value';
            $keyName = $matches[2] ?? 'field';
            
            return "A project with this $keyName already exists. Please use a different value.";
        }
        
        // Data too long error (SQLSTATE 22001)
        if (strpos($errorMessage, '22001') !== false || strpos($errorMessage, 'Data too long') !== false) {
            preg_match("/column '([^']+)'/", $errorMessage, $matches);
            $columnName = $matches[1] ?? 'unknown field';
            $friendlyName = $fieldNames[$columnName] ?? $columnName;
            
            return "The text entered for '$friendlyName' is too long. Please shorten it and try again.";
        }
        
        // Foreign key constraint error (SQLSTATE 23000)
        if (strpos($errorMessage, 'foreign key constraint fails') !== false) {
            if (strpos($errorMessage, 'client_id') !== false) {
                return "The selected client does not exist. Please select a valid client from the dropdown.";
            }
            if (strpos($errorMessage, 'sales_pic_id') !== false) {
                return "The selected Sales PIC does not exist. Please select a valid user from the dropdown.";
            }
            return "The selected value for one of the fields is invalid. Please check your selections and try again.";
        }
        
        // Default fallback with a more user-friendly message
        return "There was a problem saving your changes. Please check that all numbers are reasonable values and all required fields are filled in correctly. If the problem continues, please contact support.";
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
