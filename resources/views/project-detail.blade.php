<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Detail - Project Tracking System</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-active { background-color: #dcfce7; color: #166534; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-completed { background-color: #dbeafe; color: #1e40af; }
        .status-on-hold { background-color: #fecaca; color: #991b1b; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .tab-button.active {
            border-bottom-color: #4f46e5;
            color: #4f46e5;
        }
        .timeline-item {
            position: relative;
            padding-left: 2rem;
        }
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0.5rem;
            top: 0;
            bottom: -1rem;
            width: 2px;
            background-color: #e5e7eb;
        }
        .timeline-item:last-child::before {
            display: none;
        }
        .timeline-dot {
            position: absolute;
            left: 0;
            top: 0.25rem;
            width: 1rem;
            height: 1rem;
            border-radius: 50%;
            border: 3px solid #e5e7eb;
            background-color: white;
        }
        .timeline-dot.completed {
            background-color: #10b981;
            border-color: #10b981;
        }
        .timeline-dot.current {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
        .timeline-dot.pending {
            background-color: #fbbf24;
            border-color: #fbbf24;
        }
        .file-icon {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.5rem;
            margin-right: 0.75rem;
        }
        .file-pdf { background-color: #fef2f2; color: #dc2626; }
        .file-doc { background-color: #eff6ff; color: #2563eb; }
        .file-xls { background-color: #f0fdf4; color: #16a34a; }
        .file-img { background-color: #fefce8; color: #ca8a04; }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <h1 class="text-xl font-bold text-gray-900">Project Tracker</h1>
                    </div>
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/dashboard" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                        <a href="/projects" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Projects</a>
                        <a href="/overview" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Overview</a>
                        <a href="/finance-tracker" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Finance Tracker</a>
                        <a href="/finance-overview" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Finance Overview</a>
                        <a href="/insurance-tracker" class="text-gray-500 hover:text-gray-700 px-3 py-2 rounded-md text-sm font-medium">Insurance Tracker</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6 mb-6">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center space-x-4 mb-4">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $project->project_id }}</h1>
                        <span class="status-badge status-{{ strtolower(str_replace(' ', '-', $project->status ?? 'active')) }}">
                            {{ $project->status ?? 'Active' }}
                        </span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Client Name</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $project->client->client_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Location</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $project->location ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Category</label>
                            <p class="text-lg font-semibold text-gray-900">{{ ucfirst($project->category ?? 'N/A') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Installation Date</label>
                            <p class="text-lg font-semibold text-gray-900">{{ $project->installation_date ? \Carbon\Carbon::parse($project->installation_date)->format('Y-m-d') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                <div class="ml-6">
                    <a href="{{ route('projects.edit', $project->project_id) }}" class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center space-x-2 transition-colors">
                        <i class="ri-edit-line"></i>
                        <span>Edit Project</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button class="tab-button active py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap" data-tab="overview">
                        <i class="ri-dashboard-line mr-2"></i>Overview
                    </button>
                    <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-tab="timeline">
                        <i class="ri-time-line mr-2"></i>Timeline
                    </button>
                    <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-tab="financials">
                        <i class="ri-money-dollar-circle-line mr-2"></i>Financials
                    </button>
                    <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-tab="materials">
                        <i class="ri-package-line mr-2"></i>Materials
                    </button>
                    <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-tab="insurance">
                        <i class="ri-shield-check-line mr-2"></i>Insurance
                    </button>
                    <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-tab="documents">
                        <i class="ri-file-text-line mr-2"></i>Documents
                    </button>
                    <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap" data-tab="activity">
                        <i class="ri-history-line mr-2"></i>Activity
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div class="p-6">
                <!-- Overview Tab -->
                <div id="overview" class="tab-content active">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Client Information -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Client Information</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Company:</span>
                                    <span class="font-medium">{{ $project->client->client_name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Contact Person:</span>
                                    <span class="font-medium">{{ $project->client->contact_person ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Email:</span>
                                    <span class="font-medium">{{ $project->client->email ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Phone:</span>
                                    <span class="font-medium">{{ $project->client->phone ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Address:</span>
                                    <span class="font-medium">{{ $project->location ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Project Details -->
                        <div class="bg-gray-50 rounded-lg p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Project Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">System Capacity:</span>
                                    <span class="font-medium">{{ $project->pv_system_capacity_kwp ? $project->pv_system_capacity_kwp . ' kWp' : 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Modules:</span>
                                    <span class="font-medium">{{ $project->module_quantity ? $project->module_quantity . ' x ' : '' }}{{ $project->module ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Inverter:</span>
                                    <span class="font-medium">{{ $project->inverter ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Roof Type:</span>
                                    <span class="font-medium">{{ $project->roof_type ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Sales PIC:</span>
                                    <span class="font-medium">{{ $project->salesPic->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Start Date:</span>
                                    <span class="font-medium">{{ $project->created_at->format('Y-m-d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline Tab -->
                <div id="timeline" class="tab-content">
                    <div class="max-w-2xl">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Project Timeline</h3>
                        <div class="space-y-6">
                            <div class="timeline-item">
                                <div class="timeline-dot completed"></div>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-green-900">Design Phase</h4>
                                    <p class="text-green-700 text-sm">System design and engineering completed</p>
                                    <p class="text-green-600 text-xs mt-1">Completed on Jan 5, 2024</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot completed"></div>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-green-900">Permitting</h4>
                                    <p class="text-green-700 text-sm">Building permits and approvals obtained</p>
                                    <p class="text-green-600 text-xs mt-1">Completed on Jan 20, 2024</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot current"></div>
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-blue-900">Installation</h4>
                                    <p class="text-blue-700 text-sm">Currently installing solar panels and equipment</p>
                                    <p class="text-blue-600 text-xs mt-1">Started on Jan 25, 2024</p>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot pending"></div>
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                    <h4 class="font-semibold text-yellow-900">Commissioning</h4>
                                    <p class="text-yellow-700 text-sm">System testing and grid connection</p>
                                    <p class="text-yellow-600 text-xs mt-1">Scheduled for Feb 15, 2024</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Financials Tab -->
                <div id="financials" class="tab-content">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Financial Records</h3>
                        <button class="bg-primary hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center space-x-2 transition-colors">
                            <i class="ri-add-line"></i>
                            <span>Add Invoice</span>
                        </button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">INV-001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Initial Deposit</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$15,000.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-01-15</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge payment-paid">Paid</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                                        <button class="text-gray-600 hover:text-gray-900">Download</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">INV-002</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Equipment Purchase</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$35,000.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-02-01</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge payment-pending">Pending</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                                        <button class="text-gray-600 hover:text-gray-900">Download</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">INV-003</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Installation Services</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$8,500.00</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">2024-02-15</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge payment-overdue">Overdue</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-blue-600 hover:text-blue-900 mr-3">View</button>
                                        <button class="text-gray-600 hover:text-gray-900">Download</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Materials Tab -->
                <div id="materials" class="tab-content">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Materials & Procurement</h3>
                    
                    @if($project->projectMaterial)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Material / Component</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @php
                                        $materials = [
                                            'Klip Lok Clamp' => 'klip_lok_clamp',
                                            'L-Foot' => 'l_foot',
                                            'Tile Hook' => 'tile_hook',
                                            'Rail 2.6m' => 'rail_2_6m',
                                            'Rail 5.3m' => 'rail_5_3m',
                                            'Rail 4.7m' => 'rail_4_7m',
                                            'Rail 3.6m' => 'rail_3_6m',
                                            'Splicer' => 'splicer',
                                            'Mid Clamp' => 'mid_clamp',
                                            'End Clamp' => 'end_clamp',
                                            'Grounding Clip' => 'grounding_clip',
                                            'Grounding Lug' => 'grounding_lug',
                                            'Dongle' => 'dongle',
                                            'Precast Concrete Block' => 'precast_concrete_block',
                                            'DC Cable (4mm²)' => 'dc_cable_4mmsq',
                                            'DC Cable (6mm²)' => 'dc_cable_6mmsq',
                                            'PV Connector (Male)' => 'pv_connector_male',
                                            'PV Connector (Female)' => 'pv_connector_female',
                                            'Isolator Switch (3P)' => 'isolator_switch_3p',
                                            'kWh Meter (1-Phase)' => 'kwh_meter_1phase',
                                            'kWh Meter (3-Phase)' => 'kwh_meter_3phase',
                                            'PV AC DB' => 'pv_ac_db',
                                            'Data Logger' => 'data_logger',
                                            'Weather Station' => 'weather_station',
                                            'BESS' => 'bess',
                                            'EV Charger' => 'ev_charger',
                                            'Optimiser' => 'optimiser',
                                        ];
                                    @endphp

                                    @foreach($materials as $label => $field)
                                        @if($project->projectMaterial->$field > 0)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $label }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $project->projectMaterial->$field }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($project->projectMaterial->remark)
                            <div class="mt-6 bg-yellow-50 p-4 rounded-md border border-yellow-200">
                                <h4 class="text-sm font-medium text-yellow-800">Remarks</h4>
                                <p class="mt-1 text-sm text-yellow-700">{{ $project->projectMaterial->remark }}</p>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                            <i class="ri-file-list-3-line text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500 font-medium">No material list data imported for this project.</p>
                            <p class="text-sm text-gray-400 mt-1">Import a Material List Excel file to see data here.</p>
                        </div>
                    @endif
                </div>

                <!-- Insurance Tab -->
                <div id="insurance" class="tab-content">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Insurance Information</h3>
                    <div class="max-w-2xl">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex-shrink-0">
                                    <i class="ri-shield-check-line text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-semibold text-blue-900">Project Liability Insurance</h4>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700">Insurance Provider</label>
                                    <p class="text-blue-900 font-medium">AllState Insurance Co.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700">Policy Number</label>
                                    <p class="text-blue-900 font-medium">POL-2024-001234</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700">Coverage Amount</label>
                                    <p class="text-blue-900 font-medium">$2,000,000</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700">Expiry Date</label>
                                    <p class="text-blue-900 font-medium">December 31, 2024</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-blue-700">Coverage Details</label>
                                <p class="text-blue-900 text-sm">General liability, property damage, and workers compensation coverage for the duration of the project.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div id="documents" class="tab-content">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Project Documents</h3>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="file-icon file-pdf">
                                <i class="ri-file-pdf-line"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Project Proposal.pdf</h4>
                                <p class="text-sm text-gray-500">2.4 MB • Uploaded on Jan 1, 2024</p>
                            </div>
                            <button class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded-md text-sm font-medium">
                                <i class="ri-download-line mr-1"></i>Download
                            </button>
                        </div>
                        <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="file-icon file-doc">
                                <i class="ri-file-word-line"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Contract Agreement.docx</h4>
                                <p class="text-sm text-gray-500">1.8 MB • Uploaded on Jan 2, 2024</p>
                            </div>
                            <button class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded-md text-sm font-medium">
                                <i class="ri-download-line mr-1"></i>Download
                            </button>
                        </div>
                        <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="file-icon file-img">
                                <i class="ri-file-image-line"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Site Photos.zip</h4>
                                <p class="text-sm text-gray-500">15.2 MB • Uploaded on Jan 5, 2024</p>
                            </div>
                            <button class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded-md text-sm font-medium">
                                <i class="ri-download-line mr-1"></i>Download
                            </button>
                        </div>
                        <div class="flex items-center p-4 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            <div class="file-icon file-xls">
                                <i class="ri-file-excel-line"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">Cost Breakdown.xlsx</h4>
                                <p class="text-sm text-gray-500">856 KB • Uploaded on Jan 3, 2024</p>
                            </div>
                            <button class="text-blue-600 hover:text-blue-900 px-3 py-1 rounded-md text-sm font-medium">
                                <i class="ri-download-line mr-1"></i>Download
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Activity Tab -->
                <div id="activity" class="tab-content">
                    <h3 class="text-lg font-semibold text-gray-900 mb-6">Project Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3 p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="ri-edit-line text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Project status updated to "Active"</p>
                                <p class="text-sm text-gray-500">by Sarah Wilson • 2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="ri-check-line text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Installation milestone completed</p>
                                <p class="text-sm text-gray-500">by John Doe • 1 day ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i class="ri-file-add-line text-yellow-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Invoice INV-002 uploaded</p>
                                <p class="text-sm text-gray-500">by Mike Johnson • 2 days ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="ri-message-3-line text-purple-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Client meeting scheduled for tomorrow</p>
                                <p class="text-sm text-gray-500">by Sarah Wilson • 3 days ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3 p-4 bg-white border border-gray-200 rounded-lg">
                            <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="ri-alert-line text-red-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">Material delivery delayed</p>
                                <p class="text-sm text-gray-500">by Jane Smith • 5 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });

            // Add Invoice button functionality
            document.querySelector('button:has(span:contains("Add Invoice"))')?.addEventListener('click', function() {
                alert('Add Invoice functionality would be implemented here');
            });

            // Download button functionality
            document.querySelectorAll('button:has(i.ri-download-line)').forEach(button => {
                button.addEventListener('click', function() {
                    const fileName = this.closest('.flex').querySelector('h4').textContent;
                    alert(`Downloading ${fileName}`);
                });
            });
        });
    </script>
</body>
</html>
