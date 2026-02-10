<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Workflow - {{ $project->project_id }}</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            min-height: 100vh;
        }
        .workflow-step {
            position: relative;
            transition: all 0.3s ease;
        }
        .workflow-step.completed::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 50px;
            width: 2px;
            height: calc(100% - 20px);
            background: linear-gradient(180deg, #10b981 0%, #059669 100%);
            z-index: 0;
        }
        .workflow-step:last-child::before {
            display: none;
        }
        .step-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            position: relative;
            z-index: 1;
            transition: all 0.3s ease;
        }
        .step-icon.completed {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        .step-icon.in-progress {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            animation: pulse 2s infinite;
        }
        .step-icon.pending {
            background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
            color: #6b7280;
            border: 2px solid #d1d5db;
        }
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
            }
        }
        .progress-bar {
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            transition: width 0.5s ease;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
        }
    </style>
</head>
<body>
@include('partials.navigation')

<div class="w-full px-2 sm:px-4 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="ri-arrow-left-line text-2xl"></i>
                    </a>
                    <h1 class="text-3xl font-extrabold text-gray-900">Project Workflow</h1>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-600 ml-10">
                    <span class="font-semibold text-gray-900">{{ $project->project_id }}</span>
                    <span>•</span>
                    <span>{{ $project->client->client_name ?? 'N/A' }}</span>
                    <span>•</span>
                    <span>{{ $project->location ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('projects.edit', $project->project_id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center space-x-2 transition-colors">
                    <i class="ri-pencil-line"></i>
                    <span>Edit Project</span>
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Progress Card -->
            <div class="glass-card rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                        <i class="ri-line-chart-line text-2xl text-white"></i>
                    </div>
                    <span class="text-3xl font-extrabold text-gray-900">{{ $progressPercentage }}%</span>
                </div>
                <h3 class="text-sm font-semibold text-gray-600">Overall Progress</h3>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all" style="width: {{ $progressPercentage }}%"></div>
                </div>
            </div>

            <!-- Project Value Card -->
            <div class="glass-card rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center">
                        <i class="ri-money-dollar-circle-line text-2xl text-white"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-gray-900">
                        {{ number_format(($project->project_value_rm ?? 0) + ($project->vo_rm ?? 0), 0) }}
                    </span>
                </div>
                <h3 class="text-sm font-semibold text-gray-600">Contract Value</h3>
                <p class="text-xs text-gray-500 mt-1">RM {{ number_format($project->project_value_rm ?? 0, 0) }} + VO {{ number_format($project->vo_rm ?? 0, 0) }}</p>
            </div>

            <!-- System Capacity Card -->
            <div class="glass-card rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <i class="ri-flashlight-line text-2xl text-white"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-gray-900">{{ $project->pv_system_capacity_kwp ?? 0 }}</span>
                </div>
                <h3 class="text-sm font-semibold text-gray-600">System Capacity</h3>
                <p class="text-xs text-gray-500 mt-1">kWp PV System</p>
            </div>

            <!-- Files Card -->
            <div class="glass-card rounded-xl p-5 shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center">
                        <i class="ri-folder-line text-2xl text-white"></i>
                    </div>
                    <span class="text-2xl font-extrabold text-gray-900">{{ $project->files->count() }}</span>
                </div>
                <h3 class="text-sm font-semibold text-gray-600">Project Files</h3>
                <p class="text-xs text-gray-500 mt-1">Documents attached</p>
            </div>
        </div>

        <!-- Comprehensive Project Information Panel -->
        <div class="glass-card rounded-2xl p-6 mb-6 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                        <i class="ri-information-line text-primary"></i>
                        Project Information
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Comprehensive project details and specifications</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Project Overview -->
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="ri-file-text-line text-blue-600"></i>
                            Project Overview
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Project Name:</span>
                                <span class="text-sm text-gray-900 font-semibold text-right">{{ $project->name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Category:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ $project->category ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Scheme:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ $project->scheme ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Status:</span>
                                <span class="px-3 py-1 rounded-lg text-xs font-semibold
                                    {{ $project->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $project->status === 'In Progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $project->status === 'Planning' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ !in_array($project->status, ['Completed', 'In Progress', 'Planning']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ $project->status ?? 'N/A' }}
                                </span>
                            </div>
                            @if($project->procurement_status)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Procurement:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ $project->procurement_status }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- System Specifications -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl p-5 border border-purple-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="ri-flashlight-line text-purple-600"></i>
                            System Specifications
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">PV System Capacity:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ $project->pv_system_capacity_kwp ?? 0 }} kWp</span>
                            </div>
                            @if($project->ev_charger_capacity)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">EV Charger Capacity:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ $project->ev_charger_capacity }}</span>
                            </div>
                            @endif
                            @if($project->bess_capacity)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">BESS Capacity:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ $project->bess_capacity }}</span>
                            </div>
                            @endif
                            @if($project->module)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Module:</span>
                                <span class="text-sm text-gray-900 font-semibold text-right">{{ $project->module }}@if($project->module_quantity) ({{ $project->module_quantity }} units)@endif</span>
                            </div>
                            @endif
                            @if($project->inverter)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Inverter:</span>
                                <span class="text-sm text-gray-900 font-semibold text-right">{{ $project->inverter }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-5 border border-green-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="ri-money-dollar-circle-line text-green-600"></i>
                            Financial Summary
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Project Value:</span>
                                <span class="text-sm text-gray-900 font-bold">RM {{ number_format($project->project_value_rm ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Variation Order (VO):</span>
                                <span class="text-sm text-gray-900 font-bold">RM {{ number_format($project->vo_rm ?? 0, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-start pt-2 border-t border-green-200">
                                <span class="text-sm text-gray-700 font-semibold">Contract Total:</span>
                                <span class="text-base text-green-700 font-extrabold">RM {{ number_format(($project->project_value_rm ?? 0) + ($project->vo_rm ?? 0), 2) }}</span>
                            </div>
                            @if($project->payment_method)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Payment Method:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ $project->payment_method }}</span>
                            </div>
                            @endif
                            @if($project->contract_type)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Contract Type:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ $project->contract_type }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Team & Stakeholders -->
                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-xl p-5 border border-orange-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="ri-team-line text-orange-600"></i>
                            Team & Stakeholders
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Client:</span>
                                <span class="text-sm text-gray-900 font-semibold text-right">{{ $project->client->client_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Sales PIC:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ $project->salesPic->name ?? 'N/A' }}</span>
                            </div>
                            @if($project->installer)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Installer:</span>
                                <span class="text-sm text-gray-900 font-semibold text-right">{{ $project->installer === 'Other' ? ($project->installer_other ?? 'Other') : $project->installer }}</span>
                            </div>
                            @endif
                            @if($project->partner)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Partner:</span>
                                <span class="text-sm text-gray-900 font-semibold text-right">{{ $project->partner }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Key Dates -->
                    <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl p-5 border border-cyan-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="ri-calendar-line text-cyan-600"></i>
                            Key Dates
                        </h3>
                        <div class="space-y-3">
                            @if($project->site_survey_date)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Site Survey:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($project->site_survey_date)->format('d M Y') }}</span>
                            </div>
                            @endif
                            @if($project->installation_date)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Installation:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($project->installation_date)->format('d M Y') }}</span>
                            </div>
                            @endif
                            @if($project->closed_date)
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-600 font-medium">Closed Date:</span>
                                <span class="text-sm text-gray-900 font-semibold">{{ \Carbon\Carbon::parse($project->closed_date)->format('d M Y') }}</span>
                            </div>
                            @endif
                            @if(!$project->site_survey_date && !$project->installation_date && !$project->closed_date)
                            <div class="text-center py-4">
                                <i class="ri-calendar-close-line text-3xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">No key dates recorded yet</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="bg-gradient-to-br from-gray-50 to-slate-50 rounded-xl p-5 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="ri-file-list-line text-gray-600"></i>
                            Additional Details
                        </h3>
                        <div class="space-y-3">
                            @if($project->insurance_warranty)
                            <div>
                                <span class="text-xs text-gray-500 font-medium block mb-1">Insurance/Warranty:</span>
                                <span class="text-sm text-gray-900">{{ $project->insurance_warranty }}</span>
                            </div>
                            @endif
                            @if($project->dlp_period)
                            <div>
                                <span class="text-xs text-gray-500 font-medium block mb-1">DLP Period:</span>
                                <span class="text-sm text-gray-900">{{ $project->dlp_period }}</span>
                            </div>
                            @endif
                            @if($project->om_details)
                            <div>
                                <span class="text-xs text-gray-500 font-medium block mb-1">O&M Details:</span>
                                <span class="text-sm text-gray-900">{{ $project->om_details }}</span>
                            </div>
                            @endif
                            @if($project->services_exclusion)
                            <div>
                                <span class="text-xs text-gray-500 font-medium block mb-1">Services Exclusion:</span>
                                <span class="text-sm text-gray-900">{{ $project->services_exclusion }}</span>
                            </div>
                            @endif
                            @if($project->additional_remark)
                            <div>
                                <span class="text-xs text-gray-500 font-medium block mb-1">Additional Remarks:</span>
                                <span class="text-sm text-gray-900">{{ $project->additional_remark }}</span>
                            </div>
                            @endif
                            @if(!$project->insurance_warranty && !$project->dlp_period && !$project->om_details && !$project->services_exclusion && !$project->additional_remark)
                            <div class="text-center py-4">
                                <i class="ri-file-forbid-line text-3xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">No additional details recorded</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Progress Overview -->
        <div class="glass-card rounded-2xl p-6 mb-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Overall Progress</h2>
                    <p class="text-sm text-gray-600">Current Stage: <span class="font-semibold text-primary">{{ $currentStage }}</span></p>
                </div>
                <div class="text-right">
                    <span class="px-4 py-2 rounded-lg font-semibold text-sm
                        {{ $project->status === 'Completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $project->status === 'In Progress' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $project->status === 'Planning' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                        {{ $project->status }}
                    </span>
                </div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div class="progress-bar h-full rounded-full" style="width: {{ $progressPercentage }}%"></div>
            </div>
        </div>
    </div>

    <!-- EPCC Workflow Stages -->
    <div class="glass-card rounded-2xl p-6 mb-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">EPCC Workflow Stages</h2>
            <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">{{ count($workflowProgress) }} Stages</span>
        </div>

        <div class="space-y-4">
            @foreach($workflowProgress as $index => $stage)
            <div class="workflow-step {{ $stage['status'] === 'completed' ? 'completed' : '' }}">
                <div class="flex gap-4 items-start">
                    <!-- Step Icon -->
                    <div class="flex-shrink-0">
                        <div class="step-icon {{ $stage['status'] }}">
                            @if($stage['status'] === 'completed')
                                <i class="ri-check-line text-xl"></i>
                            @elseif($stage['status'] === 'in_progress')
                                <i class="ri-loader-4-line text-xl animate-spin"></i>
                            @else
                                <span class="text-sm font-bold">{{ $index + 1 }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Step Content -->
                    <div class="flex-1 glass-card rounded-xl p-4 {{ $stage['is_current'] ? 'ring-2 ring-primary ring-opacity-50' : '' }}">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $stage['label'] }}</h3>
                                    @if($stage['is_current'])
                                        <span class="bg-primary text-white text-xs font-semibold px-2 py-1 rounded-full">Current</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $stage['description'] }}</p>
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <i class="ri-user-line"></i>
                                        {{ $stage['responsible'] }}
                                    </span>
                                    @if($stage['date'])
                                        <span class="flex items-center gap-1">
                                            <i class="ri-calendar-line"></i>
                                            {{ \Carbon\Carbon::parse($stage['date'])->format('d M Y') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                @if($stage['status'] === 'completed')
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">Completed</span>
                                @elseif($stage['status'] === 'in_progress')
                                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-3 py-1 rounded-full">In Progress</span>
                                @else
                                    <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full">Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Project Files Section -->
    <div class="glass-card rounded-2xl p-6 mb-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="ri-folder-open-line text-primary"></i>
                    Project Documents
                </h2>
                <p class="text-sm text-gray-600 mt-1">Upload and manage files related to this project</p>
            </div>
            <button onclick="document.getElementById('fileUploadForm').classList.toggle('hidden')" 
                    class="bg-gradient-to-r from-primary to-secondary text-white px-4 py-2 rounded-lg font-medium flex items-center space-x-2 transition-all hover:shadow-lg hover:scale-105">
                <i class="ri-upload-2-line"></i>
                <span>Upload File</span>
            </button>
        </div>

        <!-- Upload Form (Hidden by default) -->
        <div id="fileUploadForm" class="hidden mb-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-200">
            <form action="{{ route('projects.files.store', $project->project_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col sm:flex-row items-start sm:items-end gap-3">
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-file-line mr-1"></i>Select File (Max 100MB)
                        </label>
                        <input type="file" 
                               name="file" 
                               required
                               class="w-full border-2 border-gray-300 rounded-lg p-2 text-sm bg-white
                                      file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                      file:text-sm file:font-semibold file:bg-primary file:text-white
                                      hover:file:bg-indigo-700 file:cursor-pointer
                                      focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary focus:ring-opacity-20">
                    </div>
                    <button type="submit" 
                            class="px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-indigo-700 transition-all flex items-center gap-2 whitespace-nowrap">
                        <i class="ri-upload-cloud-2-line"></i>
                        Upload
                    </button>
                    <button type="button" 
                            onclick="document.getElementById('fileUploadForm').classList.add('hidden')"
                            class="px-4 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition-all">
                        Cancel
                    </button>
                </div>
            </form>
        </div>

        @if(session('success'))
        <div class="mb-4 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 rounded-lg">
            <div class="flex items-center">
                <i class="ri-checkbox-circle-line text-xl mr-2"></i>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="mb-4 p-4 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 rounded-lg">
            <div class="flex items-start">
                <i class="ri-error-warning-line text-xl mr-2 mt-0.5"></i>
                <div>
                    <p class="font-semibold mb-1">Error:</p>
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <!-- Files List -->
        @if($project->files->count() > 0)
        <div class="space-y-3">
            @foreach($project->files as $file)
            <div class="flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-xl border border-gray-200 transition-all group">
                <div class="flex items-center gap-4 flex-1 min-w-0">
                    <!-- File Icon -->
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center
                        {{ Str::endsWith($file->original_name, ['.pdf']) ? 'bg-red-100 text-red-600' : '' }}
                        {{ Str::endsWith($file->original_name, ['.doc', '.docx']) ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ Str::endsWith($file->original_name, ['.xls', '.xlsx']) ? 'bg-green-100 text-green-600' : '' }}
                        {{ Str::endsWith($file->original_name, ['.jpg', '.jpeg', '.png', '.gif']) ? 'bg-purple-100 text-purple-600' : '' }}
                        {{ !Str::endsWith($file->original_name, ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.jpg', '.jpeg', '.png', '.gif']) ? 'bg-gray-200 text-gray-600' : '' }}">
                        @if(Str::endsWith($file->original_name, ['.pdf']))
                            <i class="ri-file-pdf-line text-2xl"></i>
                        @elseif(Str::endsWith($file->original_name, ['.doc', '.docx']))
                            <i class="ri-file-word-line text-2xl"></i>
                        @elseif(Str::endsWith($file->original_name, ['.xls', '.xlsx']))
                            <i class="ri-file-excel-line text-2xl"></i>
                        @elseif(Str::endsWith($file->original_name, ['.jpg', '.jpeg', '.png', '.gif']))
                            <i class="ri-image-line text-2xl"></i>
                        @else
                            <i class="ri-file-line text-2xl"></i>
                        @endif
                    </div>

                    <!-- File Info -->
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-900 truncate">{{ $file->original_name }}</h4>
                        <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
                            <span class="flex items-center gap-1">
                                <i class="ri-file-line"></i>
                                {{ number_format($file->size / 1024, 2) }} KB
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="ri-calendar-line"></i>
                                {{ $file->created_at->format('d M Y') }}
                            </span>
                            @if($file->uploader)
                            <span class="flex items-center gap-1">
                                <i class="ri-user-line"></i>
                                {{ $file->uploader->name }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('projects.files.download', [$project->project_id, $file->id]) }}" 
                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                       title="Download">
                        <i class="ri-download-2-line text-xl"></i>
                    </a>
                    <form action="{{ route('projects.files.destroy', [$project->project_id, $file->id]) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this file?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Delete">
                            <i class="ri-delete-bin-line text-xl"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-12 text-center">
            <i class="ri-folder-open-line text-6xl text-gray-300 mb-3"></i>
            <p class="text-gray-500 font-medium">No files uploaded yet</p>
            <p class="text-gray-400 text-sm mt-1">Click "Upload File" to add documents</p>
        </div>
        @endif
    </div>

    <!-- O&M Workflow Stages -->
    @if($project->handover_to_om_date || $project->om_status)
    <div class="glass-card rounded-2xl p-6 shadow-xl">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Operation & Maintenance</h2>
            <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">{{ count($omProgress) }} Stages</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($omProgress as $omStage)
            <div class="glass-card rounded-xl p-4 border-l-4 {{ $omStage['status'] === 'completed' ? 'border-green-500' : 'border-gray-300' }}">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <h3 class="font-bold text-gray-900 mb-1">{{ $omStage['label'] }}</h3>
                        <p class="text-sm text-gray-600 mb-2">{{ $omStage['description'] }}</p>
                        <div class="flex items-center gap-2 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <i class="ri-user-line"></i>
                                {{ $omStage['responsible'] }}
                            </span>
                            @if($omStage['date'])
                                <span class="flex items-center gap-1">
                                    <i class="ri-calendar-line"></i>
                                    {{ \Carbon\Carbon::parse($omStage['date'])->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    @if($omStage['status'] === 'completed')
                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">Completed</span>
                    @else
                        <span class="bg-gray-100 text-gray-600 text-xs font-semibold px-3 py-1 rounded-full">Pending</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
    // Smooth scroll to current stage on load
    document.addEventListener('DOMContentLoaded', function() {
        const currentStage = document.querySelector('.workflow-step .ring-2');
        if (currentStage) {
            currentStage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>
</body>
</html>

