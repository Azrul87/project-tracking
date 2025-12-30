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

        <!-- Progress Overview -->
        <div class="glass-card rounded-2xl p-6 mb-6 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 mb-1">Overall Progress</h2>
                    <p class="text-sm text-gray-600">Current Stage: <span class="font-semibold text-primary">{{ $currentStage }}</span></p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-extrabold text-primary mb-1">{{ $progressPercentage }}%</div>
                    <p class="text-xs text-gray-500">Completed</p>
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

