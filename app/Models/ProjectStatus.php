<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectStatus extends Model
{
    protected $fillable = [
        'project_id',
        'status_type',
        'status_value',
        'changed_by',
        'notes',
    ];

    // EPCC Workflow Status Types (based on Procedure ECN)
    const TYPE_CLIENT_ENQUIRY = 'client_enquiry';
    const TYPE_PROPOSAL_PREPARATION = 'proposal_preparation';
    const TYPE_PROPOSAL_SUBMISSION = 'proposal_submission';
    const TYPE_PROPOSAL_ACCEPTANCE = 'proposal_acceptance';
    const TYPE_LETTER_OF_AWARD = 'letter_of_award';
    const TYPE_FIRST_INVOICE = 'first_invoice';
    const TYPE_FIRST_INVOICE_PAYMENT = 'first_invoice_payment';
    const TYPE_SITE_STUDY = 'site_study';
    const TYPE_NEM_APPLICATION_SUBMISSION = 'nem_application_submission';
    const TYPE_PROJECT_PLANNING = 'project_planning';
    const TYPE_NEM_APPROVAL = 'nem_approval';
    const TYPE_ST_LICENSE_APPLICATION = 'st_license_application';
    const TYPE_SECOND_INVOICE = 'second_invoice';
    const TYPE_SECOND_INVOICE_PAYMENT = 'second_invoice_payment';
    const TYPE_MATERIAL_PROCUREMENT = 'material_procurement';
    const TYPE_SUBCON_APPOINTMENT = 'subcon_appointment';
    const TYPE_MATERIAL_DELIVERY = 'material_delivery';
    const TYPE_SITE_MOBILIZATION = 'site_mobilization';
    const TYPE_ST_LICENSE_APPROVAL = 'st_license_approval';
    const TYPE_SYSTEM_TESTING = 'system_testing';
    const TYPE_SYSTEM_COMMISSIONING = 'system_commissioning';
    const TYPE_NEM_METER_CHANGE = 'nem_meter_change';
    const TYPE_LAST_INVOICE = 'last_invoice';
    const TYPE_LAST_INVOICE_PAYMENT = 'last_invoice_payment';
    const TYPE_SYSTEM_ENERGIZE = 'system_energize';
    const TYPE_NEMCD_OBTAINED = 'nemcd_obtained';
    const TYPE_SYSTEM_TRAINING = 'system_training';
    const TYPE_PROJECT_HANDOVER_TO_CLIENT = 'project_handover_to_client';
    const TYPE_PROJECT_CLOSURE = 'project_closure';
    const TYPE_HANDOVER_TO_OM = 'handover_to_om';
    
    // O&M Workflow Status Types
    const TYPE_OM_SITE_STUDY = 'om_site_study';
    const TYPE_OM_SCHEDULE_PREPARED = 'om_schedule_prepared';
    const TYPE_OM_MONITORING = 'om_monitoring';
    const TYPE_OM_PREVENTIVE_MAINTENANCE = 'om_preventive_maintenance';
    const TYPE_OM_REPORT_SUBMISSION = 'om_report_submission';
    const TYPE_OM_SERVICES = 'om_services';
    
    // Legacy status types (kept for backward compatibility)
    const TYPE_SEDA_NEMCD = 'seda_nemcd';
    const TYPE_ST_LICENSE = 'st_license';
    const TYPE_LHDN_STAMPING = 'lhdn_stamping';
    const TYPE_GITA_APPLICATION = 'gita_application';
    const TYPE_METER_CHANGE = 'meter_change';
    const TYPE_NEM_QUOTA_APPROVAL = 'nem_quota_approval';
    const TYPE_NEM_QUOTA_SUBMISSION = 'nem_quota_submission';
    const TYPE_NEM_WELCOME_LETTER = 'nem_welcome_letter';
    const TYPE_SITE_INSTALLATION = 'site_installation';

    // Status value constants
    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED = 'Completed';
    const STATUS_NOT_STARTED = 'Not Started';

    /**
     * Get the project that owns this status.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * Get the user who changed this status.
     */
    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by', 'user_id');
    }

    /**
     * Get the latest status for a project and status type.
     */
    public static function getLatestStatus(string $projectId, string $statusType): ?self
    {
        return self::where('project_id', $projectId)
            ->where('status_type', $statusType)
            ->latest('created_at')
            ->first();
    }

    /**
     * Get all status history for a project and status type.
     */
    public static function getStatusHistory(string $projectId, string $statusType): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('project_id', $projectId)
            ->where('status_type', $statusType)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}

