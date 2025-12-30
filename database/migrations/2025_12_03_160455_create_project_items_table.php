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
        Schema::create('project_items', function (Blueprint $table) {
            // Standard Auto-Increment ID for this link
            $table->id('project_item_id');
    
            // Foreign Keys
            $table->string('project_id');
            $table->string('item_id');
    
            // Quantity needed for this specific project
            $table->integer('quantity')->default(1);
    
            $table->timestamps();
    
            // Constraints
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->foreign('item_id')->references('item_id')->on('items')->onDelete('cascade');
            
            // Optional: Ensure a project can't have the same item listed twice (prevent duplicates)
            // $table->unique(['project_id', 'item_id']); ps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_items');
    }
};
