<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ProjectFile;
use App\Models\InsurancePolicy;

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
        'invoice_status',
        'payment_status',
        'total_invoiced',
        'total_paid',
        'procurement_status',
        'module',
        'module_quantity',
        'inverter',
        'project_status',
        'site_survey_date',
        'installer',
        'installer_other',
        'installation_date',
        // EPCC Workflow Stages
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
        'handover_to_om_date',
        // O&M Workflow Stages
        'om_site_study_date',
        'om_schedule_prepared_date',
        'om_start_date',
        'om_end_date',
        'workflow_stage',
        'om_status',
    ];

    protected $casts = [
        'pv_system_capacity_kwp' => 'decimal:2',
        'project_value_rm' => 'decimal:2',
        'vo_rm' => 'decimal:2',
        'closed_date' => 'date',
        'site_survey_date' => 'date',
        'installation_date' => 'date',
        // EPCC Workflow Dates
        'client_enquiry_date' => 'date',
        'proposal_preparation_date' => 'date',
        'proposal_submission_date' => 'date',
        'proposal_acceptance_date' => 'date',
        'letter_of_award_date' => 'date',
        'first_invoice_date' => 'date',
        'first_invoice_payment_date' => 'date',
        'site_study_date' => 'date',
        'nem_application_submission_date' => 'date',
        'project_planning_date' => 'date',
        'nem_approval_date' => 'date',
        'st_license_application_date' => 'date',
        'second_invoice_date' => 'date',
        'second_invoice_payment_date' => 'date',
        'material_procurement_date' => 'date',
        'subcon_appointment_date' => 'date',
        'material_delivery_date' => 'date',
        'site_mobilization_date' => 'date',
        'st_license_approval_date' => 'date',
        'system_testing_date' => 'date',
        'system_commissioning_date' => 'date',
        'nem_meter_change_date' => 'date',
        'last_invoice_date' => 'date',
        'last_invoice_payment_date' => 'date',
        'system_energize_date' => 'date',
        'nemcd_obtained_date' => 'date',
        'system_training_date' => 'date',
        'project_handover_to_client_date' => 'date',
        'project_closure_date' => 'date',
        'handover_to_om_date' => 'date',
        // O&M Workflow Dates
        'om_site_study_date' => 'date',
        'om_schedule_prepared_date' => 'date',
        'om_start_date' => 'date',
        'om_end_date' => 'date',
    ];
    
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
     * Project items (materials) for this project.
     */
    public function projectItems(): HasMany
    {
        return $this->hasMany(ProjectItem::class, 'project_id', 'project_id');
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
}

