<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('module')->nullable()->after('procurement_status');
            $table->integer('module_quantity')->nullable()->after('module');
            $table->string('inverter')->nullable()->after('module_quantity');
            $table->string('project_status')->nullable()->after('inverter');
            $table->date('site_survey_date')->nullable()->after('project_status');
            $table->string('installer')->nullable()->after('site_survey_date');
            $table->string('installer_other')->nullable()->after('installer');
            $table->date('installation_date')->nullable()->after('installer_other');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'module',
                'module_quantity',
                'inverter',
                'project_status',
                'site_survey_date',
                'installer',
                'installer_other',
                'installation_date',
            ]);
        });
    }
};

