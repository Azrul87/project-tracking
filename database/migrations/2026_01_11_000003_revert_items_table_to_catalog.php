<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Revert the items table back to pure product catalog structure
     * by removing project-specific fields that were added in the
     * 2026_01_10_181259_expand_items_table_with_material_list_fields migration.
     */
    public function up(): void
    {
        // Get existing columns before dropping
        $columns = Schema::getColumnListing('items');
        
        Schema::table('items', function (Blueprint $table) use ($columns) {
            // Drop foreign key and indexes if they exist
            if (in_array('project_id', $columns)) {
                // Try to drop foreign key constraint (silently fail if doesn't exist)
                try {
                    $table->dropForeign(['project_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist, continue
                }
                
                // Try to drop indexes (silently fail if don't exist)
                try {
                    $table->dropIndex(['project_id']);
                } catch (\Exception $e) {
                    // Index might not exist, continue
                }
            }
            
            if (in_array('order_status', $columns)) {
                try {
                    $table->dropIndex(['order_status']);
                } catch (\Exception $e) {
                    // Continue
                }
            }
            
            if (in_array('delivery_status', $columns)) {
                try {
                    $table->dropIndex(['delivery_status']);
                } catch (\Exception $e) {
                    // Continue
                }
            }
            
            // Drop project-specific columns if they exist
            $columnsToDrop = [
                'project_id',
                'quantity',
                'unit_price',
                'total_cost',
                'supplier',
                'supplier_contact',
                'order_date',
                'expected_delivery_date',
                'actual_delivery_date',
                'order_status',
                'delivery_status',
                'notes',
                'batch_number',
                'serial_number',
                'location',
                'condition',
                'warranty_start_date',
                'warranty_end_date',
            ];
            
            $existingColumnsToDrop = array_intersect($columnsToDrop, $columns);
            
            if (!empty($existingColumnsToDrop)) {
                $table->dropColumn($existingColumnsToDrop);
            }
        });
    }

    /**
     * Reverse the migrations.
     * Re-add the project-specific fields if needed (though not recommended)
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('project_id')->nullable()->after('item_id');
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            
            $table->integer('quantity')->default(1)->after('project_id');
            $table->decimal('unit_price', 15, 2)->nullable()->after('quantity');
            $table->decimal('total_cost', 15, 2)->nullable()->after('unit_price');
            $table->string('supplier')->nullable()->after('total_cost');
            $table->string('supplier_contact')->nullable()->after('supplier');
            $table->date('order_date')->nullable()->after('supplier_contact');
            $table->date('expected_delivery_date')->nullable()->after('order_date');
            $table->date('actual_delivery_date')->nullable()->after('expected_delivery_date');
            $table->string('order_status')->nullable()->after('actual_delivery_date');
            $table->string('delivery_status')->nullable()->after('order_status');
            $table->text('notes')->nullable()->after('delivery_status');
            $table->string('batch_number')->nullable()->after('notes');
            $table->string('serial_number')->nullable()->after('batch_number');
            $table->string('location')->nullable()->after('serial_number');
            $table->string('condition')->nullable()->after('location');
            $table->date('warranty_start_date')->nullable()->after('condition');
            $table->date('warranty_end_date')->nullable()->after('warranty_start_date');
            
            $table->index('project_id');
            $table->index('order_status');
            $table->index('delivery_status');
        });
    }
};
