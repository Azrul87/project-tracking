<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add roof_type field to projects table.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Add roof_type after inverter field
            $table->string('roof_type')->nullable()->after('inverter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('roof_type');
        });
    }
};
