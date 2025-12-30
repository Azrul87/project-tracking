<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add workflow stage tracking fields based on ECN Procedure
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // EPCC Workflow Stages (based on Procedure ECN)
            $table->date('client_enquiry_date')->nullable()->after('status');
            $table->date('proposal_preparation_date')->nullable()->after('client_enquiry_date');
            $table->date('proposal_submission_date')->nullable()->after('proposal_preparation_date');
            $table->date('proposal_acceptance_date')->nullable()->after('proposal_submission_date');
            $table->date('letter_of_award_date')->nullable()->after('proposal_acceptance_date');
            $table->date('first_invoice_date')->nullable()->after('letter_of_award_date');
            $table->date('first_invoice_payment_date')->nullable()->after('first_invoice_date');
            $table->date('site_study_date')->nullable()->after('first_invoice_payment_date');
            $table->date('nem_application_submission_date')->nullable()->after('site_study_date');
            $table->date('project_planning_date')->nullable()->after('nem_application_submission_date');
            $table->date('nem_approval_date')->nullable()->after('project_planning_date');
            $table->date('st_license_application_date')->nullable()->after('nem_approval_date');
            $table->date('second_invoice_date')->nullable()->after('st_license_application_date');
            $table->date('second_invoice_payment_date')->nullable()->after('second_invoice_date');
            $table->date('material_procurement_date')->nullable()->after('second_invoice_payment_date');
            $table->date('subcon_appointment_date')->nullable()->after('material_procurement_date');
            $table->date('material_delivery_date')->nullable()->after('subcon_appointment_date');
            $table->date('site_mobilization_date')->nullable()->after('material_delivery_date');
            $table->date('st_license_approval_date')->nullable()->after('site_mobilization_date');
            $table->date('system_testing_date')->nullable()->after('st_license_approval_date');
            $table->date('system_commissioning_date')->nullable()->after('system_testing_date');
            $table->date('nem_meter_change_date')->nullable()->after('system_commissioning_date');
            $table->date('last_invoice_date')->nullable()->after('nem_meter_change_date');
            $table->date('last_invoice_payment_date')->nullable()->after('last_invoice_date');
            $table->date('system_energize_date')->nullable()->after('last_invoice_payment_date');
            $table->date('nemcd_obtained_date')->nullable()->after('system_energize_date');
            $table->date('system_training_date')->nullable()->after('nemcd_obtained_date');
            $table->date('project_handover_to_client_date')->nullable()->after('system_training_date');
            $table->date('project_closure_date')->nullable()->after('project_handover_to_client_date');
            $table->date('handover_to_om_date')->nullable()->after('project_closure_date');
            
            // O&M Workflow Stages
            $table->date('om_site_study_date')->nullable()->after('handover_to_om_date');
            $table->date('om_schedule_prepared_date')->nullable()->after('om_site_study_date');
            $table->date('om_start_date')->nullable()->after('om_schedule_prepared_date');
            $table->date('om_end_date')->nullable()->after('om_start_date');
            
            // Workflow stage status fields
            $table->string('workflow_stage')->default('Client Enquiry')->after('handover_to_om_date');
            $table->string('om_status')->nullable()->after('om_end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
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
                'om_site_study_date',
                'om_schedule_prepared_date',
                'om_start_date',
                'om_end_date',
                'workflow_stage',
                'om_status',
            ]);
        });
    }
};
