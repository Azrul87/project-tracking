<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create a dedicated table for workflow stage dates
     */
    public function up(): void
    {
        Schema::create('project_workflow_stages', function (Blueprint $table) {
            $table->id();
            $table->string('project_id');
            
            // EPCC Workflow Stages
            $table->date('client_enquiry_date')->nullable();
            $table->date('proposal_preparation_date')->nullable();
            $table->date('proposal_submission_date')->nullable();
            $table->date('proposal_acceptance_date')->nullable();
            $table->date('letter_of_award_date')->nullable();
            $table->date('first_invoice_date')->nullable();
            $table->date('first_invoice_payment_date')->nullable();
            $table->date('site_study_date')->nullable();
            $table->date('nem_application_submission_date')->nullable();
            $table->date('project_planning_date')->nullable();
            $table->date('nem_approval_date')->nullable();
            $table->date('st_license_application_date')->nullable();
            $table->date('second_invoice_date')->nullable();
            $table->date('second_invoice_payment_date')->nullable();
            $table->date('material_procurement_date')->nullable();
            $table->date('subcon_appointment_date')->nullable();
            $table->date('material_delivery_date')->nullable();
            $table->date('site_mobilization_date')->nullable();
            $table->date('st_license_approval_date')->nullable();
            $table->date('system_testing_date')->nullable();
            $table->date('system_commissioning_date')->nullable();
            $table->date('nem_meter_change_date')->nullable();
            $table->date('last_invoice_date')->nullable();
            $table->date('last_invoice_payment_date')->nullable();
            $table->date('system_energize_date')->nullable();
            $table->date('nemcd_obtained_date')->nullable();
            $table->date('system_training_date')->nullable();
            $table->date('project_handover_to_client_date')->nullable();
            $table->date('project_closure_date')->nullable();
            $table->date('handover_to_om_date')->nullable();
            
            // O&M Workflow Stages
            $table->date('om_site_study_date')->nullable();
            $table->date('om_schedule_prepared_date')->nullable();
            $table->date('om_start_date')->nullable();
            $table->date('om_end_date')->nullable();
            
            // Current workflow stage and O&M status
            $table->string('workflow_stage')->default('Client Enquiry');
            $table->string('om_status')->nullable();
            
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('project_id')
                ->references('project_id')
                ->on('projects')
                ->onDelete('cascade');
            
            // Unique constraint - one workflow record per project
            $table->unique('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_workflow_stages');
    }
};

