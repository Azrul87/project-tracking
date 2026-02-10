<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project - {{ $project->project_id }}</title>
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
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
        }
        .tab-button {
            transition: all 0.3s ease;
        }
        .tab-button.active {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
        }
        .input-field {
            transition: all 0.3s ease;
            border: 1.5px solid #e2e8f0;
        }
        .input-field:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
    </style>
</head>
<body>
@include('partials.navigation')

<div class="w-full px-2 sm:px-4 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="ri-arrow-left-line text-2xl"></i>
                    </a>
                    <h1 class="text-3xl font-extrabold text-gray-900">Edit Project</h1>
                </div>
                <p class="text-gray-600 font-medium ml-10">{{ $project->project_id }} - {{ $project->client->client_name ?? 'N/A' }}</p>
            </div>
            <a href="{{ route('projects.dashboard', $project->project_id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center space-x-2 transition-colors">
                <i class="ri-eye-line"></i>
                <span>View Workflow</span>
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm glass-card">
        <div class="flex items-center">
            <i class="ri-checkbox-circle-line text-xl mr-2"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 rounded-lg shadow-sm glass-card">
        <div class="flex items-start">
            <i class="ri-error-warning-line text-xl mr-2 mt-0.5"></i>
            <div>
                <p class="font-medium mb-2">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form id="update-project-form" action="{{ route('projects.update', $project->project_id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Tabs -->
        <div class="glass-card rounded-2xl p-2 mb-6 shadow-xl">
            <div class="flex flex-wrap gap-2">
                <button type="button" class="tab-button active px-6 py-3 rounded-xl font-semibold" onclick="switchTab('basic')">
                    <i class="ri-file-text-line mr-2"></i>Basic Info
                </button>
                <button type="button" class="tab-button px-6 py-3 rounded-xl font-semibold" onclick="switchTab('workflow')">
                    <i class="ri-flow-chart mr-2"></i>Workflow Progress
                </button>
                <button type="button" class="tab-button px-6 py-3 rounded-xl font-semibold" onclick="switchTab('technical')">
                    <i class="ri-tools-line mr-2"></i>Technical & Materials
                </button>
                <button type="button" class="tab-button px-6 py-3 rounded-xl font-semibold" onclick="switchTab('financial')">
                    <i class="ri-money-dollar-circle-line mr-2"></i>Financial
                </button>
                <button type="button" class="tab-button px-6 py-3 rounded-xl font-semibold" onclick="switchTab('operations')">
                    <i class="ri-settings-3-line mr-2"></i>Operations & O&M
                </button>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="space-y-6">
            <!-- Basic Info Tab -->
            <div id="basic-tab" class="tab-content glass-card rounded-2xl p-8 shadow-xl">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Project ID</label>
                        <input type="text" value="{{ $project->project_id }}" readonly class="input-field w-full rounded-xl p-3 border bg-gray-50 text-gray-600 cursor-not-allowed">
                        <small class="text-gray-500 text-xs">Auto-generated (cannot be changed)</small>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Project Name</label>
                        <input type="text" name="name" value="{{ old('name', $project->name) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Client <span class="text-red-500">*</span></label>
                        <select name="client_id" required class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->client_id }}" {{ old('client_id', $project->client_id) == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Sales PIC <span class="text-red-500">*</span></label>
                        <select name="sales_pic_id" required class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                            <option value="">Select Sales PIC</option>
                            @foreach($users as $user)
                            <option value="{{ $user->user_id }}" {{ old('sales_pic_id', $project->sales_pic_id) == $user->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Category</label>
                        <select name="category" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                            <option value="">Select Category</option>
                            <option value="R-PV" {{ old('category', $project->category) == 'R-PV' ? 'selected' : '' }}>R-PV</option>
                            <option value="C&I-PV" {{ old('category', $project->category) == 'C&I-PV' ? 'selected' : '' }}>C&I-PV</option>
                            <option value="EV Charger" {{ old('category', $project->category) == 'EV Charger' ? 'selected' : '' }}>EV Charger</option>
                            <option value="BESS" {{ old('category', $project->category) == 'BESS' ? 'selected' : '' }}>BESS</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Scheme</label>
                        <select name="scheme" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                            <option value="">Select Scheme</option>
                            <option value="NEM" {{ old('scheme', $project->scheme) == 'NEM' ? 'selected' : '' }}>NEM</option>
                            <option value="SELCO" {{ old('scheme', $project->scheme) == 'SELCO' ? 'selected' : '' }}>SELCO</option>
                            <option value="None" {{ old('scheme', $project->scheme) == 'None' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Location</label>
                        <input type="text" name="location" value="{{ old('location', $project->location) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">General Status</label>
                        <select name="status" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                            <option value="Planning" {{ old('status', $project->status) == 'Planning' ? 'selected' : '' }}>Planning</option>
                            <option value="In Progress" {{ old('status', $project->status) == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="Completed" {{ old('status', $project->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Workflow Progress Tab -->
            <div id="workflow-tab" class="tab-content glass-card rounded-2xl p-8 shadow-xl hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">EPCC Workflow Progress</h2>
                
                @php
                    $workflowStages = [
                        ['Client Enquiry', 'client_enquiry_date', 'BDT'],
                        ['Proposal Preparation', 'proposal_preparation_date', 'BDT/ET'],
                        ['Proposal Submission', 'proposal_submission_date', 'BDT'],
                        ['Proposal Acceptance', 'proposal_acceptance_date', 'BDT/Finance'],
                        ['Letter of Award', 'letter_of_award_date', 'BDT'],
                        ['1st Invoice', 'first_invoice_date', 'Finance'],
                        ['1st Invoice Payment', 'first_invoice_payment_date', 'Finance'],
                        ['Site Study', 'site_study_date', 'ET'],
                        ['NEM Application Submission', 'nem_application_submission_date', 'AT'],
                        ['Project Planning', 'project_planning_date', 'PT/ET'],
                        ['NEM Approval', 'nem_approval_date', 'AT'],
                        ['ST License Application', 'st_license_application_date', 'AT'],
                        ['2nd Invoice', 'second_invoice_date', 'Finance'],
                        ['2nd Invoice Payment', 'second_invoice_payment_date', 'Finance'],
                        ['Material Procurement', 'material_procurement_date', 'SC/PT'],
                        ['Subcon Appointment', 'subcon_appointment_date', 'PT'],
                        ['Material Delivery', 'material_delivery_date', 'SC'],
                        ['Site Mobilization', 'site_mobilization_date', 'PT'],
                        ['ST License Approval', 'st_license_approval_date', 'AT'],
                        ['System Testing', 'system_testing_date', 'PT'],
                        ['System Commissioning', 'system_commissioning_date', 'PT'],
                        ['NEM Meter Change', 'nem_meter_change_date', 'AT'],
                        ['Last Invoice', 'last_invoice_date', 'Finance'],
                        ['Last Invoice Payment', 'last_invoice_payment_date', 'Finance'],
                        ['System Energize', 'system_energize_date', 'PT'],
                        ['NEMCD Obtained', 'nemcd_obtained_date', 'AT'],
                        ['System Training', 'system_training_date', 'PT'],
                        ['Project Handover to Client', 'project_handover_to_client_date', 'PT'],
                        ['Project Closure', 'project_closure_date', 'PT/O&M'],
                        ['Handover to O&M', 'handover_to_om_date', 'PT/O&M'],
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($workflowStages as $stage)
                    <div class="p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-primary transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold text-gray-900 text-sm">{{ $stage[0] }}</h3>
                            <span class="text-xs bg-gray-100 px-2 py-1 rounded-full text-gray-600">{{ $stage[2] }}</span>
                        </div>
                        <input type="date" 
                               name="{{ $stage[1] }}" 
                               value="{{ old($stage[1], $project->{$stage[1]} ? $project->{$stage[1]}->format('Y-m-d') : '') }}" 
                               class="input-field w-full rounded-lg p-2 text-sm focus:outline-none bg-white">
                    </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">O&M Workflow</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-primary transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-900 text-sm">O&M Site Study</h3>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded-full text-gray-600">O&M</span>
                            </div>
                            <input type="date" name="om_site_study_date" value="{{ old('om_site_study_date', $project->om_site_study_date ? $project->om_site_study_date->format('Y-m-d') : '') }}" class="input-field w-full rounded-lg p-2 text-sm focus:outline-none bg-white">
                        </div>
                        <div class="p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-primary transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-900 text-sm">O&M Schedule Prepared</h3>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded-full text-gray-600">O&M</span>
                            </div>
                            <input type="date" name="om_schedule_prepared_date" value="{{ old('om_schedule_prepared_date', $project->om_schedule_prepared_date ? $project->om_schedule_prepared_date->format('Y-m-d') : '') }}" class="input-field w-full rounded-lg p-2 text-sm focus:outline-none bg-white">
                        </div>
                        <div class="p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-primary transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-900 text-sm">O&M Start Date</h3>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded-full text-gray-600">O&M</span>
                            </div>
                            <input type="date" name="om_start_date" value="{{ old('om_start_date', $project->om_start_date ? $project->om_start_date->format('Y-m-d') : '') }}" class="input-field w-full rounded-lg p-2 text-sm focus:outline-none bg-white">
                        </div>
                        <div class="p-4 bg-white border-2 border-gray-200 rounded-xl hover:border-primary transition-colors">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-gray-900 text-sm">O&M End Date</h3>
                                <span class="text-xs bg-gray-100 px-2 py-1 rounded-full text-gray-600">O&M</span>
                            </div>
                            <input type="date" name="om_end_date" value="{{ old('om_end_date', $project->om_end_date ? $project->om_end_date->format('Y-m-d') : '') }}" class="input-field w-full rounded-lg p-2 text-sm focus:outline-none bg-white">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical & Materials Tab -->
            <div id="technical-tab" class="tab-content glass-card rounded-2xl p-8 shadow-xl hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Technical Specifications & Materials</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-3">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">System Capacity</h3>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">PV System Capacity (kWp)</label>
                        <input type="number" name="pv_system_capacity_kwp" value="{{ old('pv_system_capacity_kwp', $project->pv_system_capacity_kwp) }}" step="0.01" min="0" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">EV Charger Capacity</label>
                        <input type="text" name="ev_charger_capacity" value="{{ old('ev_charger_capacity', $project->ev_charger_capacity) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">BESS Capacity</label>
                        <input type="text" name="bess_capacity" value="{{ old('bess_capacity', $project->bess_capacity) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    
                    <div class="lg:col-span-3 mt-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Modules & Inverter</h3>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Module</label>
                        <select name="module" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                            <option value="">Select Module</option>
                            @php
                            $modules = ['Astronergy 575Wp','Astronergy 585Wp','Astronergy 590Wp','Astronergy 605Wp','Astronergy 610Wp','Astronergy 620Wp','Jinko Solar 575Wp','Yingli Solar 585Wp','Yingli Solar 620Wp'];
                            @endphp
                            @foreach($modules as $m)
                            <option value="{{ $m }}" {{ old('module', $project->module) == $m ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Module Quantity</label>
                        <input type="number" name="module_quantity" value="{{ old('module_quantity', $project->module_quantity) }}" min="0" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Inverter</label>
                        <input type="text" name="inverter" value="{{ old('inverter', $project->inverter) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>

                    <div class="lg:col-span-3 mt-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Installation</h3>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Site Survey Date</label>
                        <input type="date" name="site_survey_date" value="{{ old('site_survey_date', $project->site_survey_date ? $project->site_survey_date->format('Y-m-d') : '') }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Installer</label>
                        <select name="installer" id="installerSelect" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white" onchange="toggleInstallerOther(this.value)">
                            <option value="">Select Installer</option>
                            @php
                            $installers = ['AR Berkat','Ax Electro','Bioserasi','Completed','PJ Plus','Other'];
                            @endphp
                            @foreach($installers as $inst)
                            <option value="{{ $inst }}" {{ old('installer', $project->installer) == $inst ? 'selected' : '' }}>{{ $inst }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="installerOtherWrapper" class="{{ old('installer', $project->installer) === 'Other' ? '' : 'hidden' }}">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Other Installer</label>
                        <input type="text" name="installer_other" value="{{ old('installer_other', $project->installer_other) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white" placeholder="Enter installer name">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Installation Date</label>
                        <input type="date" name="installation_date" value="{{ old('installation_date', $project->installation_date ? $project->installation_date->format('Y-m-d') : '') }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                </div>
            </div>

            <!-- Financial Tab -->
            <div id="financial-tab" class="tab-content glass-card rounded-2xl p-8 shadow-xl hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Financial Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Project Value (RM)</label>
                        <input type="number" name="project_value_rm" value="{{ old('project_value_rm', $project->project_value_rm) }}" step="0.01" min="0" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">VO Amount (RM)</label>
                        <input type="number" name="vo_rm" value="{{ old('vo_rm', $project->vo_rm) }}" step="0.01" min="0" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                        <input type="text" name="payment_method" value="{{ old('payment_method', $project->payment_method) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contract Type</label>
                        <input type="text" name="contract_type" value="{{ old('contract_type', $project->contract_type) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <!-- Note: Invoice Status and Payment Status are calculated from the payments table -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Procurement Status</label>
                        <input type="text" name="procurement_status" value="{{ old('procurement_status', $project->procurement_status) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Closed Date</label>
                        <input type="date" name="closed_date" value="{{ old('closed_date', $project->closed_date ? $project->closed_date->format('Y-m-d') : '') }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                </div>
            </div>

            <!-- Operations & O&M Tab -->
            <div id="operations-tab" class="tab-content glass-card rounded-2xl p-8 shadow-xl hidden">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Warranty & Operations</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Insurance/Warranty</label>
                        <input type="text" name="insurance_warranty" value="{{ old('insurance_warranty', $project->insurance_warranty) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">DLP Period</label>
                        <input type="text" name="dlp_period" value="{{ old('dlp_period', $project->dlp_period) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Partner</label>
                        <input type="text" name="partner" value="{{ old('partner', $project->partner) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">O&M Status</label>
                        <input type="text" name="om_status" value="{{ old('om_status', $project->om_status) }}" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">OM Details</label>
                        <textarea name="om_details" rows="4" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">{{ old('om_details', $project->om_details) }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Services Exclusion</label>
                        <textarea name="services_exclusion" rows="4" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">{{ old('services_exclusion', $project->services_exclusion) }}</textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Additional Remarks</label>
                        <textarea name="additional_remark" rows="4" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">{{ old('additional_remark', $project->additional_remark) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Action Buttons -->
    <div class="glass-card rounded-2xl p-6 mt-6 shadow-xl">
        <div class="flex justify-between items-center">
            <form action="{{ route('projects.destroy', $project->project_id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project? This action cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-6 py-3 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-all hover:scale-105 shadow-lg">
                    <i class="ri-delete-bin-line mr-2"></i>Delete Project
                </button>
            </form>
            <div class="flex space-x-3">
                <a href="{{ route('projects.index') }}" class="px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 transition-all hover:scale-105">Cancel</a>
                <button type="submit" form="update-project-form" class="px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-primary to-secondary rounded-xl hover:shadow-2xl transition-all hover:scale-105 shadow-lg">
                    <i class="ri-save-line mr-2"></i>Update Project
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active class to clicked button
    event.target.closest('.tab-button').classList.add('active');
}

function toggleInstallerOther(val) {
    const wrap = document.getElementById('installerOtherWrapper');
    if (!wrap) return;
    if (val === 'Other') {
        wrap.classList.remove('hidden');
    } else {
        wrap.classList.add('hidden');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    toggleInstallerOther(document.getElementById('installerSelect')?.value || '');
});
</script>
</body>
</html>
