<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove redundant fields from projects table:
     * - Payment/invoice status (should be calculated from payments table)
     * - Financial totals (should be calculated from payments table)
     * - Workflow dates (moved to project_workflow_stages table)
     */
    public function up(): void
    {
        // Check which columns exist before dropping
        $columns = Schema::getColumnListing('projects');
        $columnsToDrop = [];
        
        // Payment and invoice status fields
        $paymentFields = ['invoice_status', 'payment_status', 'total_invoiced', 'total_paid'];
        foreach ($paymentFields as $field) {
            if (in_array($field, $columns)) {
                $columnsToDrop[] = $field;
            }
        }
        
        // Workflow stage fields
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
            if (in_array($field, $columns)) {
                $columnsToDrop[] = $field;
            }
        }
        
        // Drop columns in batches to avoid issues
        if (!empty($columnsToDrop)) {
            Schema::table('projects', function (Blueprint $table) use ($columnsToDrop) {
                $table->dropColumn($columnsToDrop);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Re-add payment and invoice status fields
            $table->string('invoice_status')->nullable()->after('status');
            $table->string('payment_status')->nullable()->after('invoice_status');
            $table->decimal('total_invoiced', 15, 2)->nullable()->after('payment_status');
            $table->decimal('total_paid', 15, 2)->nullable()->after('total_invoiced');
            
            // Re-add workflow stage fields
            $table->date('client_enquiry_date')->nullable()->after('status');
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
            $table->date('om_site_study_date')->nullable();
            $table->date('om_schedule_prepared_date')->nullable();
            $table->date('om_start_date')->nullable();
            $table->date('om_end_date')->nullable();
            $table->string('workflow_stage')->default('Client Enquiry')->nullable();
            $table->string('om_status')->nullable();
        });
    }
};

