<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate existing status data from projects table to project_statuses table
     */
    public function up(): void
    {
        // Get all projects (we'll check each status field individually)
        $projects = DB::table('projects')->get();

        $statusTypes = [
            'seda_nemcd_status' => ProjectStatus::TYPE_SEDA_NEMCD,
            'st_license_status' => ProjectStatus::TYPE_ST_LICENSE,
            'lhdn_stamping_status' => ProjectStatus::TYPE_LHDN_STAMPING,
            'gita_application_status' => ProjectStatus::TYPE_GITA_APPLICATION,
            'meter_change_status' => ProjectStatus::TYPE_METER_CHANGE,
            'nem_quota_approval_status' => ProjectStatus::TYPE_NEM_QUOTA_APPROVAL,
            'nem_quota_submission_status' => ProjectStatus::TYPE_NEM_QUOTA_SUBMISSION,
            'nem_welcome_letter_status' => ProjectStatus::TYPE_NEM_WELCOME_LETTER,
            'site_installation_status' => ProjectStatus::TYPE_SITE_INSTALLATION,
        ];

        foreach ($projects as $project) {
            foreach ($statusTypes as $columnName => $statusType) {
                if (!empty($project->$columnName)) {
                    DB::table('project_statuses')->insert([
                        'project_id' => $project->project_id,
                        'status_type' => $statusType,
                        'status_value' => $project->$columnName,
                        'changed_by' => null, // We don't know who changed it originally
                        'notes' => 'Migrated from projects table',
                        'created_at' => $project->created_at ?? now(),
                        'updated_at' => $project->updated_at ?? now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete migrated data
        DB::table('project_statuses')->where('notes', 'Migrated from projects table')->delete();
    }
};

