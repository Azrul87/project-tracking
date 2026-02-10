<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate existing workflow data from projects table to project_workflow_stages table
     */
    public function up(): void
    {
        // Check if workflow columns exist before migrating
        $columns = Schema::getColumnListing('projects');
        
        if (in_array('client_enquiry_date', $columns)) {
            // Migrate data for each project
            $projects = DB::table('projects')->get();
            
            foreach ($projects as $project) {
                DB::table('project_workflow_stages')->insert([
                    'project_id' => $project->project_id,
                    'client_enquiry_date' => $project->client_enquiry_date ?? null,
                    'proposal_preparation_date' => $project->proposal_preparation_date ?? null,
                    'proposal_submission_date' => $project->proposal_submission_date ?? null,
                    'proposal_acceptance_date' => $project->proposal_acceptance_date ?? null,
                    'letter_of_award_date' => $project->letter_of_award_date ?? null,
                    'first_invoice_date' => $project->first_invoice_date ?? null,
                    'first_invoice_payment_date' => $project->first_invoice_payment_date ?? null,
                    'site_study_date' => $project->site_study_date ?? null,
                    'nem_application_submission_date' => $project->nem_application_submission_date ?? null,
                    'project_planning_date' => $project->project_planning_date ?? null,
                    'nem_approval_date' => $project->nem_approval_date ?? null,
                    'st_license_application_date' => $project->st_license_application_date ?? null,
                    'second_invoice_date' => $project->second_invoice_date ?? null,
                    'second_invoice_payment_date' => $project->second_invoice_payment_date ?? null,
                    'material_procurement_date' => $project->material_procurement_date ?? null,
                    'subcon_appointment_date' => $project->subcon_appointment_date ?? null,
                    'material_delivery_date' => $project->material_delivery_date ?? null,
                    'site_mobilization_date' => $project->site_mobilization_date ?? null,
                    'st_license_approval_date' => $project->st_license_approval_date ?? null,
                    'system_testing_date' => $project->system_testing_date ?? null,
                    'system_commissioning_date' => $project->system_commissioning_date ?? null,
                    'nem_meter_change_date' => $project->nem_meter_change_date ?? null,
                    'last_invoice_date' => $project->last_invoice_date ?? null,
                    'last_invoice_payment_date' => $project->last_invoice_payment_date ?? null,
                    'system_energize_date' => $project->system_energize_date ?? null,
                    'nemcd_obtained_date' => $project->nemcd_obtained_date ?? null,
                    'system_training_date' => $project->system_training_date ?? null,
                    'project_handover_to_client_date' => $project->project_handover_to_client_date ?? null,
                    'project_closure_date' => $project->project_closure_date ?? null,
                    'handover_to_om_date' => $project->handover_to_om_date ?? null,
                    'om_site_study_date' => $project->om_site_study_date ?? null,
                    'om_schedule_prepared_date' => $project->om_schedule_prepared_date ?? null,
                    'om_start_date' => $project->om_start_date ?? null,
                    'om_end_date' => $project->om_end_date ?? null,
                    'workflow_stage' => $project->workflow_stage ?? 'Client Enquiry',
                    'om_status' => $project->om_status ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Migrate data back to projects table if needed
        // This would require the columns to still exist, so we'll just clear the workflow_stages table
        DB::table('project_workflow_stages')->truncate();
    }
};

