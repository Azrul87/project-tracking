<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Rename old project_materials table and create new normalized pivot table.
     */
    public function up(): void
    {
        // Rename the old table to keep data safe during migration
        // Only rename if the original exists and the backup doesn't
        if (Schema::hasTable('project_materials') && !Schema::hasTable('project_materials_old')) {
            Schema::rename('project_materials', 'project_materials_old');
        }
        
        // Create new normalized pivot table
        if (!Schema::hasTable('project_materials')) {
            Schema::create('project_materials', function (Blueprint $table) {
                $table->id();
                $table->string('project_id');
                $table->foreignId('material_id')->constrained('materials')->onDelete('cascade');
                $table->integer('quantity')->default(0);
                $table->text('remark')->nullable();
                $table->timestamps();
                
                // Foreign key to projects
                // We use a custom constraint name to avoid collision with the old table's constraint
                $table->foreign('project_id', 'fk_pm_new_project_id')->references('project_id')->on('projects')->onDelete('cascade');
                
                // Unique constraint - each project can have each material only once
                $table->unique(['project_id', 'material_id']);
                
                // Indexes for performance
                $table->index('project_id');
                $table->index('material_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_materials');
        Schema::rename('project_materials_old', 'project_materials');
    }
};
