<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate data from old wide table to new normalized pivot table.
     */
    public function up(): void
    {
        // Get all materials with their IDs
        $materials = DB::table('materials')->get()->keyBy('code');
        
        // Material field names from old table
        $materialFields = [
            'klip_lok_clamp', 'l_foot', 'tile_hook',
            'rail_2_6m', 'rail_5_3m', 'rail_4_7m', 'rail_3_6m',
            'splicer', 'mid_clamp', 'end_clamp',
            'grounding_clip', 'grounding_lug',
            'dongle', 'precast_concrete_block',
            'dc_cable_4mmsq', 'dc_cable_6mmsq',
            'pv_connector_male', 'pv_connector_female',
            'isolator_switch_3p', 'kwh_meter_1phase', 'kwh_meter_3phase', 'pv_ac_db',
            'data_logger', 'weather_station', 'bess',
            'ev_charger', 'optimiser',
        ];
        
        // Get all old project materials records
        $oldRecords = DB::table('project_materials_old')->get();
        
        echo "Migrating " . $oldRecords->count() . " project materials records...\n";
        
        $migratedCount = 0;
        
        foreach ($oldRecords as $oldRecord) {
            $projectId = $oldRecord->project_id;
            $remark = $oldRecord->remark ?? null;
            
            // For each material field, create a pivot record if quantity > 0
            foreach ($materialFields as $field) {
                $quantity = $oldRecord->$field ?? 0;
                
                if ($quantity > 0 && isset($materials[$field])) {
                    DB::table('project_materials')->insert([
                        'project_id' => $projectId,
                        'material_id' => $materials[$field]->id,
                        'quantity' => $quantity,
                        'remark' => $remark, // Attach remark to all materials (can be cleaned up later if needed)
                        'created_at' => $oldRecord->created_at ?? now(),
                        'updated_at' => $oldRecord->updated_at ?? now(),
                    ]);
                    
                    $migratedCount++;
                }
            }
        }
        
        echo "Successfully migrated $migratedCount material quantities!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear the new table and restore from old backup
        DB::table('project_materials')->truncate();
        
        // Note: Data will be restored from project_materials_old when down() of previous migration runs
    }
};
