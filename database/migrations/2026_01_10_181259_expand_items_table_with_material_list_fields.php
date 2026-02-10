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
        Schema::table('items', function (Blueprint $table) {
            // Add project_id to link items directly to projects (merging project_items)
            $table->string('project_id')->nullable()->after('item_id');
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            
            // Material List specific fields
            $table->integer('quantity')->default(1)->after('project_id');
            $table->decimal('unit_price', 15, 2)->nullable()->after('quantity');
            $table->decimal('total_cost', 15, 2)->nullable()->after('unit_price');
            $table->string('supplier')->nullable()->after('total_cost');
            $table->string('supplier_contact')->nullable()->after('supplier');
            $table->date('order_date')->nullable()->after('supplier_contact');
            $table->date('expected_delivery_date')->nullable()->after('order_date');
            $table->date('actual_delivery_date')->nullable()->after('expected_delivery_date');
            $table->string('order_status')->nullable()->after('actual_delivery_date'); // e.g., "Pending", "Ordered", "Delivered", "Cancelled"
            $table->string('delivery_status')->nullable()->after('order_status'); // e.g., "Pending", "In Transit", "Delivered", "Partial"
            $table->text('notes')->nullable()->after('delivery_status');
            $table->string('batch_number')->nullable()->after('notes');
            $table->string('serial_number')->nullable()->after('batch_number');
            $table->string('location')->nullable()->after('serial_number'); // Storage location/warehouse
            $table->string('condition')->nullable()->after('location'); // e.g., "New", "Used", "Refurbished"
            $table->date('warranty_start_date')->nullable()->after('condition');
            $table->date('warranty_end_date')->nullable()->after('warranty_start_date');
            
            // Add index for faster queries
            $table->index('project_id');
            $table->index('order_status');
            $table->index('delivery_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropIndex(['project_id']);
            $table->dropIndex(['order_status']);
            $table->dropIndex(['delivery_status']);
            
            $table->dropColumn([
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
            ]);
        });
    }
};
