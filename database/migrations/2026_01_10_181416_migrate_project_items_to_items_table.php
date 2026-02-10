<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate data from project_items to items table
        // Since items can be used in multiple projects, we need to create separate item records for each project
        
        // First, update existing items that are only in one project
        DB::statement("
            UPDATE items i
            INNER JOIN (
                SELECT item_id, MIN(project_id) as project_id, MIN(quantity) as quantity
                FROM project_items
                GROUP BY item_id
                HAVING COUNT(DISTINCT project_id) = 1
            ) pi ON i.item_id = pi.item_id
            SET i.project_id = pi.project_id,
                i.quantity = pi.quantity
            WHERE i.project_id IS NULL
        ");
        
        // For items used in multiple projects, create new item records for each project
        // Generate new item_ids for duplicates
        DB::statement("
            INSERT INTO items (
                item_id, project_id, quantity, name, type, brand, model, unit, 
                warranty_details, stock_total_amount, stock_delivered, stock_current_need,
                created_at, updated_at
            )
            SELECT 
                CONCAT(pi.item_id, '-', pi.project_id) as item_id,
                pi.project_id,
                pi.quantity,
                COALESCE(i.name, pi.item_id) as name,
                COALESCE(i.type, 'Other') as type,
                i.brand,
                i.model,
                COALESCE(i.unit, 'pcs') as unit,
                i.warranty_details,
                i.stock_total_amount,
                i.stock_delivered,
                i.stock_current_need,
                COALESCE(i.created_at, NOW()),
                NOW()
            FROM project_items pi
            INNER JOIN items i ON pi.item_id = i.item_id
            WHERE i.project_id IS NOT NULL
            AND NOT EXISTS (
                SELECT 1 FROM items i2 
                WHERE i2.item_id = CONCAT(pi.item_id, '-', pi.project_id)
            )
        ");
        
        // Handle items in project_items that don't exist in items table
        DB::statement("
            INSERT INTO items (item_id, project_id, quantity, name, type, unit, created_at, updated_at)
            SELECT 
                pi.item_id,
                pi.project_id,
                pi.quantity,
                pi.item_id as name,
                'Other' as type,
                'pcs' as unit,
                NOW(),
                NOW()
            FROM project_items pi
            WHERE NOT EXISTS (
                SELECT 1 FROM items i WHERE i.item_id = pi.item_id
            )
            GROUP BY pi.item_id, pi.project_id, pi.quantity
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate project_items from items that have project_id
        DB::statement("
            INSERT INTO project_items (project_id, item_id, quantity, created_at, updated_at)
            SELECT project_id, item_id, quantity, created_at, updated_at
            FROM items
            WHERE project_id IS NOT NULL
        ");
        
        // Clear project_id from items table
        DB::statement("UPDATE items SET project_id = NULL, quantity = 1 WHERE project_id IS NOT NULL");
    }
};
