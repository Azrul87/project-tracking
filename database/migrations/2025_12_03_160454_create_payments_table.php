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
        Schema::create('payments', function (Blueprint $table) {
            // Primary Key (e.g., "pay_abc123")
            $table->string('payment_id')->primary();
    
            // Foreign Key to Projects
            $table->string('project_id');
    
            // Payment Details
            $table->string('description'); // e.g., "1st Payment", "VO Payment"
            
            $table->date('invoice_date')->nullable();
            $table->decimal('invoice_amount', 15, 2)->nullable();
            
            $table->date('payment_date')->nullable();
            $table->decimal('payment_amount', 15, 2)->nullable();
    
            $table->timestamps();
    
            // Link to Projects table
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
