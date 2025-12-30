<?php

namespace App\Helpers;

use App\Models\Project;
use App\Models\ProjectStatus;

class WorkflowHelper
{
    /**
     * Get all EPCC workflow stages in order
     */
    public static function getEPCCWorkflowStages(): array
    {
        return [
            Project::WORKFLOW_CLIENT_ENQUIRY => [
                'label' => 'Client Enquiry',
                'responsible' => 'BDT',
                'status_type' => ProjectStatus::TYPE_CLIENT_ENQUIRY,
                'date_field' => 'client_enquiry_date',
                'description' => 'Contact client to understand client\'s requirement',
            ],
            Project::WORKFLOW_PROPOSAL_PREPARATION => [
                'label' => 'Proposal Preparation',
                'responsible' => 'BDT/ET',
                'status_type' => ProjectStatus::TYPE_PROPOSAL_PREPARATION,
                'date_field' => 'proposal_preparation_date',
                'description' => 'Prepare preliminary design and business proposal',
            ],
            Project::WORKFLOW_PROPOSAL_SUBMISSION => [
                'label' => 'Proposal Submission',
                'responsible' => 'BDT',
                'status_type' => ProjectStatus::TYPE_PROPOSAL_SUBMISSION,
                'date_field' => 'proposal_submission_date',
                'description' => 'Submit business proposal to client',
            ],
            Project::WORKFLOW_PROPOSAL_ACCEPTANCE => [
                'label' => 'Proposal Acceptance',
                'responsible' => 'BDT/Finance',
                'status_type' => ProjectStatus::TYPE_PROPOSAL_ACCEPTANCE,
                'date_field' => 'proposal_acceptance_date',
                'description' => 'Client signs proposal or issues Letter of Award. 1st Invoice issued.',
            ],
            Project::WORKFLOW_SITE_STUDY => [
                'label' => 'Site Study',
                'responsible' => 'ET',
                'status_type' => ProjectStatus::TYPE_SITE_STUDY,
                'date_field' => 'site_study_date',
                'description' => 'Conduct site study for engineering design and authority submission',
            ],
            Project::WORKFLOW_NEM_APPLICATION => [
                'label' => 'NEM Application Submission',
                'responsible' => 'AT',
                'status_type' => ProjectStatus::TYPE_NEM_APPLICATION_SUBMISSION,
                'date_field' => 'nem_application_submission_date',
                'description' => 'Prepare documents for NEM quota application submission to SEDA',
            ],
            Project::WORKFLOW_PROJECT_PLANNING => [
                'label' => 'Project Planning',
                'responsible' => 'PT/ET',
                'status_type' => ProjectStatus::TYPE_PROJECT_PLANNING,
                'date_field' => 'project_planning_date',
                'description' => 'Plan for project execution and complete detailed engineering design',
            ],
            Project::WORKFLOW_NEM_APPROVAL => [
                'label' => 'NEM Approval',
                'responsible' => 'AT',
                'status_type' => ProjectStatus::TYPE_NEM_APPROVAL,
                'date_field' => 'nem_approval_date',
                'description' => 'NEM Quota approval received from SEDA',
            ],
            Project::WORKFLOW_ST_LICENSE_APPLICATION => [
                'label' => 'ST License Application',
                'responsible' => 'AT',
                'status_type' => ProjectStatus::TYPE_ST_LICENSE_APPLICATION,
                'date_field' => 'st_license_application_date',
                'description' => 'ST License application submitted',
            ],
            Project::WORKFLOW_MATERIAL_PROCUREMENT => [
                'label' => 'Material Procurement & Subcon Appointment',
                'responsible' => 'SC/PT/ET',
                'status_type' => ProjectStatus::TYPE_MATERIAL_PROCUREMENT,
                'date_field' => 'material_procurement_date',
                'description' => 'Procure materials and appoint subcon for installation works',
            ],
            Project::WORKFLOW_SITE_INSTALLATION => [
                'label' => 'Material Delivery & Site Mobilization',
                'responsible' => 'SC/PT',
                'status_type' => ProjectStatus::TYPE_SITE_MOBILIZATION,
                'date_field' => 'site_mobilization_date',
                'description' => 'Material arrived and site installation works begin',
            ],
            Project::WORKFLOW_SYSTEM_TESTING => [
                'label' => 'System Testing & Commissioning',
                'responsible' => 'PT',
                'status_type' => ProjectStatus::TYPE_SYSTEM_TESTING,
                'date_field' => 'system_testing_date',
                'description' => 'Conduct testing and commissioning on site',
            ],
            Project::WORKFLOW_METER_CHANGE => [
                'label' => 'NEM Meter Change',
                'responsible' => 'AT',
                'status_type' => ProjectStatus::TYPE_NEM_METER_CHANGE,
                'date_field' => 'nem_meter_change_date',
                'description' => 'Trigger NEM meter change process with TNB and arrange meter change',
            ],
            Project::WORKFLOW_SYSTEM_ENERGIZE => [
                'label' => 'System Energize',
                'responsible' => 'PT/Finance',
                'status_type' => ProjectStatus::TYPE_SYSTEM_ENERGIZE,
                'date_field' => 'system_energize_date',
                'description' => 'Last invoice issued and system energized',
            ],
            Project::WORKFLOW_NEMCD_OBTAINED => [
                'label' => 'Obtain NEMCD from SEDA',
                'responsible' => 'AT',
                'status_type' => ProjectStatus::TYPE_NEMCD_OBTAINED,
                'date_field' => 'nemcd_obtained_date',
                'description' => 'NEMCD obtained from SEDA',
            ],
            Project::WORKFLOW_SYSTEM_TRAINING => [
                'label' => 'System Training & Project Handover to Client',
                'responsible' => 'PT',
                'status_type' => ProjectStatus::TYPE_SYSTEM_TRAINING,
                'date_field' => 'system_training_date',
                'description' => 'Conduct system training to client and handover project',
            ],
            Project::WORKFLOW_PROJECT_CLOSURE => [
                'label' => 'Project Closure',
                'responsible' => 'PT/O&M',
                'status_type' => ProjectStatus::TYPE_PROJECT_CLOSURE,
                'date_field' => 'project_closure_date',
                'description' => 'Project closure and handover to O&M personnel',
            ],
        ];
    }

    /**
     * Get all O&M workflow stages in order
     */
    public static function getOMWorkflowStages(): array
    {
        return [
            'om_handover' => [
                'label' => 'Project Handover to O&M',
                'responsible' => 'PT/O&M',
                'status_type' => ProjectStatus::TYPE_HANDOVER_TO_OM,
                'date_field' => 'handover_to_om_date',
                'description' => 'Project handed over to O&M personnel',
            ],
            'om_site_study' => [
                'label' => 'O&M Site Study',
                'responsible' => 'PT/O&M',
                'status_type' => ProjectStatus::TYPE_OM_SITE_STUDY,
                'date_field' => 'om_site_study_date',
                'description' => 'Conduct site study to understand the system installed',
            ],
            'om_schedule' => [
                'label' => 'Prepare O&M Schedule',
                'responsible' => 'O&M',
                'status_type' => ProjectStatus::TYPE_OM_SCHEDULE_PREPARED,
                'date_field' => 'om_schedule_prepared_date',
                'description' => 'Prepare schedule and plan for system maintenance',
            ],
            'om_monitoring' => [
                'label' => 'Regular System Monitoring',
                'responsible' => 'O&M',
                'status_type' => ProjectStatus::TYPE_OM_MONITORING,
                'date_field' => null,
                'description' => 'Monitor system daily for performance',
            ],
            'om_maintenance' => [
                'label' => 'Preventive Maintenance & System Cleaning',
                'responsible' => 'O&M',
                'status_type' => ProjectStatus::TYPE_OM_PREVENTIVE_MAINTENANCE,
                'date_field' => null,
                'description' => 'Perform preventive maintenance and system cleaning as scheduled',
            ],
            'om_report' => [
                'label' => 'Report Submission',
                'responsible' => 'O&M',
                'status_type' => ProjectStatus::TYPE_OM_REPORT_SUBMISSION,
                'date_field' => null,
                'description' => 'Submit maintenance report to client',
            ],
        ];
    }

    /**
     * Get current workflow stage for a project
     */
    public static function getCurrentWorkflowStage(Project $project): ?string
    {
        return $project->workflow_stage ?? Project::WORKFLOW_CLIENT_ENQUIRY;
    }

    /**
     * Get next workflow stage
     */
    public static function getNextWorkflowStage(string $currentStage): ?string
    {
        $stages = array_keys(self::getEPCCWorkflowStages());
        $currentIndex = array_search($currentStage, $stages);
        
        if ($currentIndex !== false && isset($stages[$currentIndex + 1])) {
            return $stages[$currentIndex + 1];
        }
        
        return null;
    }

    /**
     * Get previous workflow stage
     */
    public static function getPreviousWorkflowStage(string $currentStage): ?string
    {
        $stages = array_keys(self::getEPCCWorkflowStages());
        $currentIndex = array_search($currentStage, $stages);
        
        if ($currentIndex !== false && $currentIndex > 0) {
            return $stages[$currentIndex - 1];
        }
        
        return null;
    }

    /**
     * Update project workflow stage
     */
    public static function updateWorkflowStage(Project $project, string $stage, ?string $userId = null): void
    {
        $project->workflow_stage = $stage;
        $project->save();

        // Create status record
        $stages = self::getEPCCWorkflowStages();
        if (isset($stages[$stage])) {
            $stageInfo = $stages[$stage];
            $project->setStatus(
                $stageInfo['status_type'],
                ProjectStatus::STATUS_IN_PROGRESS,
                $userId,
                "Workflow stage updated to: {$stageInfo['label']}"
            );
        }
    }
}

