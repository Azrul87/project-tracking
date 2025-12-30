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
        Schema::table('projects', function (Blueprint $table) {
            // Status tracking fields for different categories
            $table->string('seda_nemcd_status')->nullable()->after('procurement_status');
            $table->string('st_license_status')->nullable()->after('seda_nemcd_status');
            $table->string('lhdn_stamping_status')->nullable()->after('st_license_status');
            $table->string('gita_application_status')->nullable()->after('lhdn_stamping_status');
            $table->string('meter_change_status')->nullable()->after('gita_application_status');
            $table->string('nem_quota_approval_status')->nullable()->after('meter_change_status');
            $table->string('nem_quota_submission_status')->nullable()->after('nem_quota_approval_status');
            $table->string('nem_welcome_letter_status')->nullable()->after('nem_quota_submission_status');
            $table->string('site_installation_status')->nullable()->after('nem_welcome_letter_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'seda_nemcd_status',
                'st_license_status',
                'lhdn_stamping_status',
                'gita_application_status',
                'meter_change_status',
                'nem_quota_approval_status',
                'nem_quota_submission_status',
                'nem_welcome_letter_status',
                'site_installation_status',
            ]);
        });
    }
};
