<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectWorkflowStage extends Model
{
    protected $fillable = [
        'project_id',
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

    /**
     * Get the project that owns this workflow stage.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }
}

