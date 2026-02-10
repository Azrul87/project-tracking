<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\ProjectFile;
use App\Models\InsurancePolicy;
use App\Models\ProjectWorkflowStage;
use App\Models\ProjectMaterial;
use App\Models\Material;

class Project extends Model
{
    protected $primaryKey = 'project_id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'project_id';
    }

    protected $fillable = [
        'project_id',
        'client_id',
        'sales_pic_id',
        'name',
        'category',
        'scheme',
        'location',
        'pv_system_capacity_kwp',
        'ev_charger_capacity',
        'bess_capacity',
        'project_value_rm',
        'vo_rm',
        'payment_method',
        'payment_type',
        'contract_type',
        'insurance_warranty',
        'dlp_period',
        'om_details',
        'partner',
        'services_exclusion',
        'additional_remark',
        'closed_date',
        'status',
        'procurement_status',
        'module',
        'module_quantity',
        'inverter',
        'project_status',
        'site_survey_date',
        'installer',
        'installer_other',
        'installation_date',
        'roof_type',
    ];

    protected $casts = [
        'pv_system_capacity_kwp' => 'decimal:2',
        'project_value_rm' => 'decimal:2',
        'vo_rm' => 'decimal:2',
        'closed_date' => 'date',
        'site_survey_date' => 'date',
        'installation_date' => 'date',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically create workflow stage record when a project is created
        static::created(function ($project) {
            $workflowData = [
                'project_id' => $project->project_id,
                'workflow_stage' => self::WORKFLOW_CLIENT_ENQUIRY,
            ];
            
            // Apply any pending workflow updates from __set() method
            if (isset($project->pendingWorkflowUpdates)) {
                $workflowData = array_merge($workflowData, $project->pendingWorkflowUpdates);
                unset($project->pendingWorkflowUpdates);
            }
            
            ProjectWorkflowStage::create($workflowData);
        });
    }
    
    // Workflow Stage Constants (based on Procedure ECN)
    const WORKFLOW_CLIENT_ENQUIRY = 'Client Enquiry';
    const WORKFLOW_PROPOSAL_PREPARATION = 'Proposal Preparation';
    const WORKFLOW_PROPOSAL_SUBMISSION = 'Proposal Submission';
    const WORKFLOW_PROPOSAL_ACCEPTANCE = 'Proposal Acceptance';
    const WORKFLOW_SITE_STUDY = 'Site Study';
    const WORKFLOW_NEM_APPLICATION = 'NEM Application Submission';
    const WORKFLOW_PROJECT_PLANNING = 'Project Planning';
    const WORKFLOW_NEM_APPROVAL = 'NEM Approval';
    const WORKFLOW_ST_LICENSE_APPLICATION = 'ST License Application';
    const WORKFLOW_MATERIAL_PROCUREMENT = 'Material Procurement';
    const WORKFLOW_SITE_INSTALLATION = 'Site Installation';
    const WORKFLOW_SYSTEM_TESTING = 'System Testing & Commissioning';
    const WORKFLOW_METER_CHANGE = 'NEM Meter Change';
    const WORKFLOW_SYSTEM_ENERGIZE = 'System Energize';
    const WORKFLOW_NEMCD_OBTAINED = 'Obtain NEMCD from SEDA';
    const WORKFLOW_SYSTEM_TRAINING = 'System Training';
    const WORKFLOW_PROJECT_HANDOVER = 'Project Handover to Client';
    const WORKFLOW_PROJECT_CLOSURE = 'Project Closure';
    const WORKFLOW_HANDOVER_TO_OM = 'Handover to O&M';
    const WORKFLOW_OM_MONITORING = 'O&M Monitoring';
    const WORKFLOW_OM_MAINTENANCE = 'O&M Maintenance';

    // Status constants
    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_NOT_STARTED = 'Not Started';

    /**
     * Get the client that owns this project.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    /**
     * Get the sales PIC (user) for this project.
     */
    public function salesPic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sales_pic_id', 'user_id');
    }

    /**
     * Get all statuses for this project.
     */
    public function statuses(): HasMany
    {
        return $this->hasMany(ProjectStatus::class, 'project_id', 'project_id');
    }

    /**
     * Files uploaded for this project.
     */
    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class, 'project_id', 'project_id');
    }

    /**
     * Insurance policies for this project.
     */
    public function insurancePolicies(): HasMany
    {
        return $this->hasMany(InsurancePolicy::class, 'project_id', 'project_id');
    }

    /**
     * Payments for this project.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'project_id', 'project_id');
    }

    /**
     * Items (materials) for this project.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'project_id', 'project_id');
    }

    /**
     * Workflow stages for this project.
     */
    public function workflowStage(): HasOne
    {
        return $this->hasOne(ProjectWorkflowStage::class, 'project_id', 'project_id');
    }

    /**
     * Materials for this project (normalized many-to-many relationship).
     */
    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(
            Material::class,           // Related model
            'project_materials',       // Pivot table
            'project_id',              // Foreign key on pivot table for this model
            'material_id',             // Foreign key on pivot table for related model
            'project_id'               // Parent key on this model (custom primary key)
        )
        ->withPivot('quantity', 'remark')
        ->withTimestamps();
    }

    /**
     * Get project materials pivot records directly.
     */
    public function projectMaterials(): HasMany
    {
        return $this->hasMany(ProjectMaterial::class, 'project_id', 'project_id');
    }

    /**
     * Legacy accessor for backward compatibility.
     * Returns the materials relationship with eager loading.
     */
    public function getProjectMaterialAttribute()
    {
        // For backward compatibility, return a wrapper that behaves like the old single record
        // but actually contains the normalized data
        if (!$this->relationLoaded('materials')) {
            $this->load('materials');
        }
        
        return $this->materials;
    }

    /**
     * Get the latest status for a specific status type.
     */
    public function getLatestStatus(string $statusType): ?ProjectStatus
    {
        return $this->statuses()
            ->where('status_type', $statusType)
            ->latest('created_at')
            ->first();
    }

    /**
     * Get status value for a specific status type (convenience method).
     */
    public function getStatusValue(string $statusType): ?string
    {
        $status = $this->getLatestStatus($statusType);
        return $status ? $status->status_value : null;
    }

    /**
     * Set status for a specific status type.
     */
    public function setStatus(string $statusType, string $statusValue, ?string $changedBy = null, ?string $notes = null): ProjectStatus
    {
        return ProjectStatus::create([
            'project_id' => $this->project_id,
            'status_type' => $statusType,
            'status_value' => $statusValue,
            'changed_by' => $changedBy ?? auth()->id(),
            'notes' => $notes,
        ]);
    }

    /**
     * Generate a unique project ID.
     */
    public static function generateProjectId(): string
    {
        $prefix = 'PRJ-';
        $year = date('Y');
        $lastProject = self::where('project_id', 'like', $prefix . $year . '%')
            ->orderBy('project_id', 'desc')
            ->first();

        if ($lastProject) {
            // Extract the number part after PRJ-YYYY
            $lastNumber = (int) substr($lastProject->project_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get or create workflow stage record for this project.
     */
    public function getOrCreateWorkflowStage(): ProjectWorkflowStage
    {
        // First, try to get from loaded relationship
        if ($this->relationLoaded('workflowStage')) {
            $workflow = $this->getRelation('workflowStage');
            if ($workflow instanceof ProjectWorkflowStage) {
                return $workflow;
            }
        }
        
        // Try to get existing workflow stage from database
        $workflow = ProjectWorkflowStage::where('project_id', $this->project_id)->first();
        
        if ($workflow instanceof ProjectWorkflowStage) {
            // Refresh the relationship cache
            $this->setRelation('workflowStage', $workflow);
            return $workflow;
        }
        
        // Create new workflow stage if it doesn't exist
        $workflow = ProjectWorkflowStage::create([
            'project_id' => $this->project_id,
            'workflow_stage' => self::WORKFLOW_CLIENT_ENQUIRY,
        ]);
        
        // Cache the relationship
        $this->setRelation('workflowStage', $workflow);
        
        return $workflow;
    }

    /**
     * Calculate total invoiced amount from payments table.
     */
    public function getTotalInvoicedAttribute(): float
    {
        return (float) $this->payments()->sum('invoice_amount');
    }

    /**
     * Calculate total paid amount from payments table.
     */
    public function getTotalPaidAttribute(): float
    {
        return (float) $this->payments()->sum('payment_amount');
    }

    /**
     * Calculate payment status based on payments.
     */
    public function getPaymentStatusAttribute(): string
    {
        $payments = $this->payments;
        $payCount = $payments->whereNotNull('payment_date')->count();
        
        if ($payCount === 0) {
            return 'Pending 1st Payment';
        }
        
        $totalPaid = $this->total_paid;
        $contractValue = ($this->project_value_rm ?? 0) + ($this->vo_rm ?? 0);
        
        if ($totalPaid >= $contractValue) {
            return 'Fully Paid';
        }
        
        // Check for overdue payments
        $today = now()->startOfDay();
        $invoices = $payments->whereNotNull('invoice_date')->sortBy('invoice_date');
        $cumulativeInvoice = 0;
        $cumulativePaid = 0;
        
        foreach ($invoices as $payment) {
            $cumulativeInvoice += (float) ($payment->invoice_amount ?? 0);
            $cumulativePaid += (float) ($payment->payment_amount ?? 0);
            
            if ($payment->invoice_date && $payment->invoice_date->lt($today) && $cumulativePaid < $cumulativeInvoice) {
                return 'Overdue';
            }
        }
        
        return "$payCount Payment(s) Received";
    }

    /**
     * Calculate invoice status based on payments.
     */
    public function getInvoiceStatusAttribute(): string
    {
        $invoices = $this->payments()->whereNotNull('invoice_date')->get();
        $invoiceCount = $invoices->count();
        
        if ($invoiceCount === 0) {
            return 'No Invoices';
        }
        
        $totalInvoiced = $this->total_invoiced;
        $totalPaid = $this->total_paid;
        
        if ($totalPaid >= $totalInvoiced) {
            return 'All Invoiced & Paid';
        }
        
        return "$invoiceCount Invoice(s) Issued";
    }

    /**
     * Get workflow stage name (for backward compatibility).
     */
    public function getWorkflowStageAttribute(): ?string
    {
        try {
            $workflow = $this->relationLoaded('workflowStage') 
                ? $this->getRelation('workflowStage')
                : $this->workflowStage;
            
            if ($workflow instanceof ProjectWorkflowStage) {
                return $workflow->workflow_stage ?? self::WORKFLOW_CLIENT_ENQUIRY;
            }
        } catch (\Exception $e) {
            // If anything goes wrong, return default
        }
        
        return self::WORKFLOW_CLIENT_ENQUIRY;
    }

    /**
     * Get O&M status (for backward compatibility).
     */
    public function getOmStatusAttribute(): ?string
    {
        try {
            $workflow = $this->relationLoaded('workflowStage') 
                ? $this->getRelation('workflowStage')
                : $this->workflowStage;
            
            if ($workflow instanceof ProjectWorkflowStage) {
                return $workflow->om_status ?? null;
            }
        } catch (\Exception $e) {
            // If anything goes wrong, return null
        }
        
        return null;
    }

    /**
     * Magic method to access workflow date fields through relationship.
     * This provides backward compatibility for code that accesses $project->client_enquiry_date, etc.
     */
    public function __get($key)
    {
        // Check if it's a workflow date field
        $workflowDateFields = [
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
        ];
        
        if (in_array($key, $workflowDateFields)) {
            try {
                // Check if we have a project_id (even if not saved yet)
                if (!empty($this->project_id)) {
                    // Try to get from loaded relationship first
                    if ($this->relationLoaded('workflowStage')) {
                        $workflow = $this->getRelation('workflowStage');
                        if ($workflow instanceof ProjectWorkflowStage) {
                            return $workflow->$key ?? null;
                        }
                    }
                    
                    // Always get fresh data from database to ensure we have the latest updates
                    $workflow = ProjectWorkflowStage::where('project_id', $this->project_id)->first();
                    if ($workflow instanceof ProjectWorkflowStage) {
                        // Update the cached relationship with fresh data
                        $this->setRelation('workflowStage', $workflow);
                        // Access the attribute - ProjectWorkflowStage model will handle date casting
                        return $workflow->$key ?? null;
                    }
                }
            } catch (\Exception $e) {
                // If anything goes wrong, just return null
                \Log::error('Error accessing workflow date field', [
                    'key' => $key,
                    'project_id' => $this->project_id ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
                return null;
            }
            
            return null;
        }
        
        return parent::__get($key);
    }

    /**
     * Magic method to set workflow date fields through relationship.
     */
    public function __set($key, $value)
    {
        $workflowDateFields = [
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
        
        if (in_array($key, $workflowDateFields)) {
            // If project doesn't exist yet, store the value to be saved later
            if (!$this->exists) {
                // Store in a temporary array to be processed after project is saved
                if (!isset($this->pendingWorkflowUpdates)) {
                    $this->pendingWorkflowUpdates = [];
                }
                $this->pendingWorkflowUpdates[$key] = $value;
                return;
            }
            
            // Project exists, update workflow stage directly
            ProjectWorkflowStage::updateOrCreate(
                ['project_id' => $this->project_id],
                [$key => $value]
            );
            
            // Clear the relationship cache
            $this->unsetRelation('workflowStage');
            return;
        }
        
        parent::__set($key, $value);
    }
}

