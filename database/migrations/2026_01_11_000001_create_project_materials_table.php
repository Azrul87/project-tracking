<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create project_materials table to store material quantities for each project.
     */
    public function up(): void
    {
        Schema::create('project_materials', function (Blueprint $table) {
            $table->id();
            $table->string('project_id');
            $table->foreign('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            
            // Mounting & Racking Materials
            $table->integer('klip_lok_clamp')->nullable()->default(0);
            $table->integer('l_foot')->nullable()->default(0);
            $table->integer('tile_hook')->nullable()->default(0);
            $table->integer('rail_2_6m')->nullable()->default(0);
            $table->integer('rail_5_3m')->nullable()->default(0);
            $table->integer('rail_4_7m')->nullable()->default(0);
            $table->integer('rail_3_6m')->nullable()->default(0);
            
            // Clamps & Connectors
            $table->integer('splicer')->nullable()->default(0);
            $table->integer('mid_clamp')->nullable()->default(0);
            $table->integer('end_clamp')->nullable()->default(0);
            
            // Grounding
            $table->integer('grounding_clip')->nullable()->default(0);
            $table->integer('grounding_lug')->nullable()->default(0);
            
            // Structural
            $table->integer('dongle')->nullable()->default(0);
            $table->integer('precast_concrete_block')->nullable()->default(0);
            
            // Cables
            $table->integer('dc_cable_4mmsq')->nullable()->default(0);
            $table->integer('dc_cable_6mmsq')->nullable()->default(0);
            
            // PV Connectors
            $table->integer('pv_connector_male')->nullable()->default(0);
            $table->integer('pv_connector_female')->nullable()->default(0);
            
            // Electrical Components
            $table->integer('isolator_switch_3p')->nullable()->default(0);
            $table->integer('kwh_meter_1phase')->nullable()->default(0);
            $table->integer('kwh_meter_3phase')->nullable()->default(0);
            $table->integer('pv_ac_db')->nullable()->default(0);
            
            // Monitoring & Storage
            $table->integer('data_logger')->nullable()->default(0);
            $table->integer('weather_station')->nullable()->default(0);
            $table->integer('bess')->nullable()->default(0);
            
            // Additional Systems
            $table->integer('ev_charger')->nullable()->default(0);
            $table->integer('optimiser')->nullable()->default(0);
            
            // Notes
            $table->text('remark')->nullable();
            
            $table->timestamps();
            
            // Add index for faster queries
            $table->index('project_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_materials');
    }
};
