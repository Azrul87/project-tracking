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
        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('project_id');
            $table->string('status_type'); // seda_nemcd, st_license, lhdn_stamping, gita_application, etc.
            $table->string('status_value')->nullable(); // Pending, In Progress, Completed, Not Started
            $table->string('changed_by')->nullable(); // user_id who made the change
            $table->text('notes')->nullable(); // Optional notes about the change
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->foreign('changed_by')->references('user_id')->on('users')->onDelete('set null');
            
            // Indexes for better query performance
            $table->index(['project_id', 'status_type']);
            $table->index('status_type');
            $table->index('status_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_statuses');
    }
};

