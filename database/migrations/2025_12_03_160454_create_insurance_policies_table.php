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
        Schema::create('insurance_policies', function (Blueprint $table) {
            // Primary Key (e.g., "pol_xyz789")
            $table->string('policy_id')->primary();
    
            // Foreign Key to Projects
            $table->string('project_id');
    
            // Policy Details
            $table->string('provider_name')->nullable(); // e.g., "MSIG"
            $table->string('policy_number')->nullable();
            $table->date('policy_date')->nullable();
            $table->string('description')->nullable(); // e.g., "1st Year", "Renwal"
    
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
        Schema::dropIfExists('insurance_policies');
    }
};
