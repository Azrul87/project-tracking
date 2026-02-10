<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create materials catalog table to store all material types.
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., 'klip_lok_clamp'
            $table->string('name'); // e.g., 'Klip Lok Clamp'
            $table->string('category'); // e.g., 'Mounting & Racking'
            $table->string('unit')->default('pcs'); // e.g., 'pcs', 'meter', 'set'
            $table->text('description')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index('category');
            $table->index('code');
        });
        
        // Seed with existing 24 materials
        $materials = [
            // Mounting & Racking
            ['code' => 'klip_lok_clamp', 'name' => 'Klip Lok Clamp', 'category' => 'Mounting & Racking', 'unit' => 'pcs'],
            ['code' => 'l_foot', 'name' => 'L-Foot', 'category' => 'Mounting & Racking', 'unit' => 'pcs'],
            ['code' => 'tile_hook', 'name' => 'Tile Hook', 'category' => 'Mounting & Racking', 'unit' => 'pcs'],
            ['code' => 'rail_2_6m', 'name' => 'Rail 2.6m', 'category' => 'Mounting & Racking', 'unit' => 'pcs'],
            ['code' => 'rail_5_3m', 'name' => 'Rail 5.3m', 'category' => 'Mounting & Racking', 'unit' => 'pcs'],
            ['code' => 'rail_4_7m', 'name' => 'Rail 4.7m', 'category' => 'Mounting & Racking', 'unit' => 'pcs'],
            ['code' => 'rail_3_6m', 'name' => 'Rail 3.6m', 'category' => 'Mounting & Racking', 'unit' => 'pcs'],
            
            // Clamps & Connectors
            ['code' => 'splicer', 'name' => 'Splicer', 'category' => 'Clamps & Connectors', 'unit' => 'pcs'],
            ['code' => 'mid_clamp', 'name' => 'Mid Clamp', 'category' => 'Clamps & Connectors', 'unit' => 'pcs'],
            ['code' => 'end_clamp', 'name' => 'End Clamp', 'category' => 'Clamps & Connectors', 'unit' => 'pcs'],
            
            // Grounding
            ['code' => 'grounding_clip', 'name' => 'Grounding Clip', 'category' => 'Grounding', 'unit' => 'pcs'],
            ['code' => 'grounding_lug', 'name' => 'Grounding Lug', 'category' => 'Grounding', 'unit' => 'pcs'],
            
            // Structural
            ['code' => 'dongle', 'name' => 'Dongle', 'category' => 'Structural', 'unit' => 'pcs'],
            ['code' => 'precast_concrete_block', 'name' => 'Precast Concrete Block', 'category' => 'Structural', 'unit' => 'pcs'],
            
            // Cables
            ['code' => 'dc_cable_4mmsq', 'name' => 'DC Cable (4mm²)', 'category' => 'Cables', 'unit' => 'meter'],
            ['code' => 'dc_cable_6mmsq', 'name' => 'DC Cable (6mm²)', 'category' => 'Cables', 'unit' => 'meter'],
            
            // PV Connectors
            ['code' => 'pv_connector_male', 'name' => 'PV Connector (Male)', 'category' => 'PV Connectors', 'unit' => 'pcs'],
            ['code' => 'pv_connector_female', 'name' => 'PV Connector (Female)', 'category' => 'PV Connectors', 'unit' => 'pcs'],
            
            // Electrical Components
            ['code' => 'isolator_switch_3p', 'name' => 'Isolator Switch (3P)', 'category' => 'Electrical Components', 'unit' => 'pcs'],
            ['code' => 'kwh_meter_1phase', 'name' => 'kWh Meter (1-Phase)', 'category' => 'Electrical Components', 'unit' => 'pcs'],
            ['code' => 'kwh_meter_3phase', 'name' => 'kWh Meter (3-Phase)', 'category' => 'Electrical Components', 'unit' => 'pcs'],
            ['code' => 'pv_ac_db', 'name' => 'PV AC DB', 'category' => 'Electrical Components', 'unit' => 'pcs'],
            
            // Monitoring & Storage
            ['code' => 'data_logger', 'name' => 'Data Logger', 'category' => 'Monitoring & Storage', 'unit' => 'pcs'],
            ['code' => 'weather_station', 'name' => 'Weather Station', 'category' => 'Monitoring & Storage', 'unit' => 'pcs'],
            ['code' => 'bess', 'name' => 'BESS', 'category' => 'Monitoring & Storage', 'unit' => 'pcs'],
            
            // Additional Systems
            ['code' => 'ev_charger', 'name' => 'EV Charger', 'category' => 'Additional Systems', 'unit' => 'pcs'],
            ['code' => 'optimiser', 'name' => 'Optimiser', 'category' => 'Additional Systems', 'unit' => 'pcs'],
        ];
        
        foreach ($materials as $material) {
            DB::table('materials')->insert(array_merge($material, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
