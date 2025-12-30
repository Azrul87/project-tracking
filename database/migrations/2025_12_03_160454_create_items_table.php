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
        Schema::create('items', function (Blueprint $table) {
            // Primary Key (e.g., "item_ast575")
            $table->string('item_id')->primary(); 
    
            // Product Details
            $table->string('name'); // e.g., "Astronergy 575Wp"
            $table->string('type'); // e.g., "Module", "Inverter", "Clamp"
            $table->string('brand')->nullable(); // e.g., "Astronergy"
            $table->string('model')->nullable(); 
            $table->string('unit')->default('pcs'); // pcs, meter, set
            $table->string('warranty_details')->nullable(); // e.g., "12 years"
    
            // Inventory / Warehouse Data
            $table->integer('stock_total_amount')->default(0); // Total bought
            $table->integer('stock_delivered')->default(0);    // Total used/sent to site
            $table->integer('stock_current_need')->default(0); // Calculated need
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
