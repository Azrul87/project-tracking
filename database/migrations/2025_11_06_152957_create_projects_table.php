<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            // Primary Key (Using Project No like "2303005")
            $table->string('project_id')->primary(); 
    
            // Foreign Keys (We define the relationships here)
            $table->string('client_id'); 
            $table->string('sales_pic_id');
    
            // Core Fields
            $table->string('name')->nullable(); // e.g., "Solar for Wong Kim Mei"
            $table->string('category')->nullable(); // R-PV, C&I-PV
            $table->string('scheme')->nullable(); // NEM, SELCO
            $table->string('location')->nullable(); // Selangor, Pahang
            
            // Technical Specs
            $table->decimal('pv_system_capacity_kwp', 10, 2)->nullable();
            $table->string('ev_charger_capacity')->nullable();
            $table->string('bess_capacity')->nullable();
            
            // Financials
            $table->decimal('project_value_rm', 15, 2)->nullable();
            $table->decimal('vo_rm', 15, 2)->nullable(); // Variation Order
            $table->string('payment_method')->nullable();
            $table->string('contract_type')->nullable();
            
            // Warranty & Ops
            $table->string('insurance_warranty')->nullable();
            $table->string('dlp_period')->nullable();
            $table->text('om_details')->nullable();
            $table->string('partner')->nullable();
            $table->text('services_exclusion')->nullable();
            $table->text('additional_remark')->nullable();
            
            // Dates & Status
            $table->date('closed_date')->nullable();
            $table->string('status')->default('Planning'); // Planning, In Progress, Completed
            
            // Status fields from other sheets
            $table->string('invoice_status')->nullable();
            $table->string('payment_status')->nullable();
            $table->string('procurement_status')->nullable();
    
            $table->timestamps();
    
            // Foreign Key Constraints (Links tables together)
            $table->foreign('client_id')->references('client_id')->on('clients')->onDelete('cascade');
            $table->foreign('sales_pic_id')->references('user_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
