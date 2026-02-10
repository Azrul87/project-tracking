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
        Schema::dropIfExists('project_items');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('project_items', function (Blueprint $table) {
            $table->id('project_item_id');
            $table->string('project_id');
            $table->string('item_id');
            $table->integer('quantity')->default(1);
            $table->timestamps();
            
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->foreign('item_id')->references('item_id')->on('items')->onDelete('cascade');
        });
    }
};
