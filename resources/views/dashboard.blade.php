<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Project Dashboard</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
<style>
:where([class^="ri-"])::before { content: "\f3c2"; }
body {
font-family: 'Inter', sans-serif;
}
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
-webkit-appearance: none;
margin: 0;
}
input[type="number"] {
-moz-appearance: textfield;
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
.gantt-chart .task {
height: 24px;
border-radius: 4px;
margin: 2px 0;
}
.task-on-track {
background-color: rgba(87, 181, 231, 0.8);
}
.task-at-risk {
background-color: rgba(251, 191, 114, 0.8);
}
.task-delayed {
background-color: rgba(252, 141, 98, 0.8);
}
.timeline-dot {
width: 12px;
height: 12px;
border-radius: 50%;
}
.timeline-line {
width: 2px;
background-color: #e5e7eb;
}
.milestone-completed {
background-color: #10b981;
}
.milestone-upcoming {
background-color: #6366f1;
}
.milestone-overdue {
background-color: #ef4444;
}
.custom-switch {
position: relative;
display: inline-block;
width: 36px;
height: 20px;
}
.custom-switch input {
opacity: 0;
width: 0;
height: 0;
}
.switch-slider {
position: absolute;
cursor: pointer;
top: 0;
left: 0;
right: 0;
bottom: 0;
background-color: #e5e7eb;
transition: .4s;
border-radius: 20px;
}
.switch-slider:before {
position: absolute;
content: "";
height: 16px;
width: 16px;
left: 2px;
bottom: 2px;
background-color: white;
transition: .4s;
border-radius: 50%;
}
input:checked + .switch-slider {
background-color: #4f46e5;
}
input:checked + .switch-slider:before {
transform: translateX(16px);
}
.custom-checkbox {
appearance: none;
width: 18px;
height: 18px;
border: 2px solid #d1d5db;
border-radius: 4px;
cursor: pointer;
position: relative;
}
.custom-checkbox:checked {
background-color: #4f46e5;
border-color: #4f46e5;
}
.custom-checkbox:checked::after {
content: "";
position: absolute;
left: 5px;
top: 2px;
width: 6px;
height: 10px;
border: solid white;
border-width: 0 2px 2px 0;
transform: rotate(45deg);
}
#weeklyProgressChart, #taskStatusChart {
width: 100%;
min-width: 0;
}
</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')
<!-- Main Content -->
<main class="container mx-auto px-6 py-6">
@if(session('success'))
<div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
@endif
<div class="grid grid-cols-12 gap-6">
<!-- System Overview Card -->
<div class="col-span-12 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-start mb-6">
<div>
<h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
<div class="text-sm text-gray-600 mt-1">Summary of all projects in the system</div>
</div>
</div>

<!-- Summary Statistics Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
<div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg border border-blue-200">
<div class="text-sm text-blue-700 font-medium mb-1">Total Projects</div>
<div class="text-3xl font-extrabold text-blue-900">{{ $stats['total_projects'] ?? 0 }}</div>
<div class="text-xs text-blue-600 mt-1">
<span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-1"></span>
{{ $stats['projects_planning'] ?? 0 }} Planning • 
{{ $stats['projects_in_progress'] ?? 0 }} In Progress • 
{{ $stats['projects_completed'] ?? 0 }} Completed
</div>
</div>

<div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
<div class="text-sm text-green-700 font-medium mb-1">Contract Value</div>
<div class="text-2xl font-extrabold text-green-900">RM {{ number_format($stats['total_contract_value'] ?? 0, 0) }}</div>
<div class="text-xs text-green-600 mt-1">
Paid: RM {{ number_format($stats['total_paid'] ?? 0, 0) }}
</div>
</div>

<div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
<div class="text-sm text-purple-700 font-medium mb-1">Outstanding</div>
<div class="text-2xl font-extrabold text-purple-900">RM {{ number_format($stats['total_outstanding'] ?? 0, 0) }}</div>
<div class="text-xs text-purple-600 mt-1">
{{ $stats['total_contract_value'] > 0 ? round(($stats['total_outstanding'] / $stats['total_contract_value']) * 100, 1) : 0 }}% of total
</div>
</div>

<div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
<div class="text-sm text-orange-700 font-medium mb-1">Workflow Progress</div>
<div class="text-3xl font-extrabold text-orange-900">{{ round($stats['workflow_progress'] ?? 0) }}%</div>
<div class="text-xs text-orange-600 mt-1">
{{ $stats['total_completed_stages'] ?? 0 }}/{{ $stats['total_possible_stages'] ?? 0 }} stages
</div>
</div>
</div>

<!-- Overall Progress -->
<div class="mb-6">
<div class="flex justify-between items-center mb-2">
<div class="text-sm font-medium text-gray-700">Overall System Progress</div>
<div class="text-sm font-medium text-primary">{{ round($stats['overall_progress'] ?? 0) }}%</div>
</div>
<div class="w-full bg-gray-200 rounded-full h-3">
<div class="bg-gradient-to-r from-primary to-secondary h-3 rounded-full transition-all" style="width: {{ min(100, $stats['overall_progress'] ?? 0) }}%"></div>
</div>
<div class="flex justify-between items-center mt-2 text-xs text-gray-600">
<span>Workflow: {{ round($stats['workflow_progress'] ?? 0) }}%</span>
<span>Payment: {{ round($stats['payment_progress'] ?? 0) }}%</span>
</div>
</div>

<!-- Payment & Workflow Stats -->
<div class="grid grid-cols-2 gap-4">
<div class="bg-gray-50 p-4 rounded">
<div class="text-sm text-gray-500 mb-1">Payment Progress</div>
<div class="space-y-2">
<div class="flex items-center justify-between">
<div class="text-sm text-gray-600">Total Invoiced</div>
<div class="text-sm font-medium text-blue-600">RM {{ number_format($stats['total_invoiced'] ?? 0, 0) }}</div>
</div>
<div class="flex items-center justify-between">
<div class="text-sm text-gray-600">Total Paid</div>
<div class="text-sm font-medium text-green-600">RM {{ number_format($stats['total_paid'] ?? 0, 0) }}</div>
</div>
<div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
<div class="bg-primary h-1.5 rounded-full" style="width: {{ min(100, $stats['payment_progress'] ?? 0) }}%"></div>
</div>
</div>
</div>
<div class="bg-gray-50 p-4 rounded">
<div class="text-sm text-gray-500 mb-1">Workflow Stages</div>
<div class="flex items-end justify-between">
<div class="text-xl font-semibold text-gray-800">{{ $stats['total_completed_stages'] ?? 0 }}/{{ $stats['total_possible_stages'] ?? 0 }}</div>
<div class="text-sm text-gray-600">{{ round($stats['workflow_progress'] ?? 0) }}% completed</div>
</div>
</div>
</div>
</div>
@php
// Prepare chart data
$weeklyLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
$weeklyPlanned = [15, 30, 45, 60];
$weeklyActual = [12, 25, 42, 54];

if (isset($chartData['weekly_progress']) && !empty($chartData['weekly_progress'])) {
    $labels = array_column($chartData['weekly_progress'], 'label');
    $planned = array_column($chartData['weekly_progress'], 'planned');
    $actual = array_column($chartData['weekly_progress'], 'actual');
    
    if (!empty($labels)) {
        $weeklyLabels = $labels;
    }
    if (!empty($planned)) {
        $weeklyPlanned = $planned;
    }
    if (!empty($actual)) {
        $weeklyActual = $actual;
    }
}
@endphp

<!-- Charts Section -->
<div class="col-span-12 grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
<div class="bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-4">
<h3 class="text-sm font-medium text-gray-700">Weekly Progress</h3>
<button class="text-xs text-gray-500 hover:text-gray-700">
Last 4 weeks
</button>
</div>
<div id="weeklyProgressChart" style="width: 100%; height: 256px; min-width: 0;"></div>
</div>
<div class="bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-4">
<h3 class="text-sm font-medium text-gray-700">Task Status</h3>
<button class="text-xs text-gray-500 hover:text-gray-700">
View all
</button>
</div>
<div id="taskStatusChart" style="width: 100%; height: 256px; min-width: 0;"></div>
</div>
</div>
<!-- Team Members Panel removed per user request -->
<!--
<div class="col-span-12 lg:col-span-4 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-4">
<h2 class="text-lg font-semibold text-gray-800">Team Members</h2>
<button class="text-sm text-primary hover:text-indigo-700 whitespace-nowrap">View all</button>
</div>
<div class="space-y-4 mb-6">
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20young%20business%20woman%20with%20short%20brown%20hair%2C%20neutral%20expression%2C%20business%20attire%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=1&orientation=squarish" alt="Emily Parker" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">Emily Parker</div>
<div class="text-xs text-gray-500">Project Manager</div>
<div id="teamWorkloadChart" class="h-48"></div>
</div>
</div>
-->
<!-- <button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20an%20asian%20man%20in%20his%2030s%2C%20short%20black%20hair%2C%20wearing%20business%20casual%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=2&orientation=squarish" alt="David Chen" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">David Chen</div>
<div class="text-xs text-gray-500">Lead Designer</div>
</div>
</div>
<button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20black%20woman%20in%20her%2020s%20with%20natural%20hair%2C%20wearing%20business%20casual%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=3&orientation=squarish" alt="Sophia Williams" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-gray-300 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">Sophia Williams</div>
<div class="text-xs text-gray-500">Content Strategist</div>
</div>
</div>
<button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20caucasian%20man%20in%20his%2040s%20with%20glasses%20and%20short%20brown%20hair%2C%20wearing%20business%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=4&orientation=squarish" alt="Michael Johnson" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">Michael Johnson</div>
<div class="text-xs text-gray-500">SEO Specialist</div>
</div>
</div>
<button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20latina%20woman%20in%20her%2030s%20with%20long%20dark%20hair%2C%20wearing%20business%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=5&orientation=squarish" alt="Isabella Rodriguez" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-yellow-500 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">Isabella Rodriguez</div>
<div class="text-xs text-gray-500">Social Media Manager</div>
</div>
</div>
<button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
</div>
<div>
<div class="flex justify-between items-center mb-4">
<h3 class="text-sm font-medium text-gray-700">Team Workload</h3>
<button class="text-xs text-gray-500 hover:text-gray-700">
This week
</button>
 </div> -->
</div>
<!-- Recent Projects Section -->
<div class="col-span-12 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Recent Projects</h2>
<a href="{{ route('projects.index') }}" class="text-sm text-primary hover:text-indigo-700 whitespace-nowrap">View all</a>
</div>
@if($recentProjects && $recentProjects->count() > 0)
<div class="overflow-x-auto">
<table class="min-w-full">
<thead class="bg-gray-50 border-b border-gray-200">
<tr>
<th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Project ID</th>
<th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Client</th>
<th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Location</th>
<th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
<th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Contract Value</th>
<th class="px-4 py-3 text-left text-xs font-semibold text-gray-700">Progress</th>
<th class="px-4 py-3 text-right text-xs font-semibold text-gray-700">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-100">
@foreach($recentProjects as $proj)
@php
$projContract = ($proj->project_value_rm ?? 0) + ($proj->vo_rm ?? 0);
$projPaid = $proj->payments->sum('payment_amount') ?? 0;
$projProgress = $projContract > 0 ? ($projPaid / $projContract) * 100 : 0;
@endphp
<tr class="hover:bg-gray-50 transition-colors">
<td class="px-4 py-3">
<span class="font-semibold text-primary">{{ $proj->project_id }}</span>
</td>
<td class="px-4 py-3 text-sm text-gray-700">
{{ $proj->client->client_name ?? 'N/A' }}
</td>
<td class="px-4 py-3 text-sm text-gray-600">
{{ $proj->location ?? 'N/A' }}
</td>
<td class="px-4 py-3">
@php
$statusColors = [
    'Planning' => 'bg-yellow-100 text-yellow-800',
    'In Progress' => 'bg-blue-100 text-blue-800',
    'Completed' => 'bg-green-100 text-green-800',
];
$color = $statusColors[$proj->status] ?? 'bg-gray-100 text-gray-800';
@endphp
<span class="px-2 py-1 {{ $color }} rounded-full text-xs font-semibold">
{{ $proj->status ?? 'N/A' }}
</span>
</td>
<td class="px-4 py-3 text-sm text-gray-700">
RM {{ number_format($projContract, 0) }}
</td>
<td class="px-4 py-3">
<div class="flex items-center gap-2">
<div class="flex-1 bg-gray-200 rounded-full h-2">
<div class="bg-primary h-2 rounded-full" style="width: {{ min(100, $projProgress) }}%"></div>
</div>
<span class="text-xs text-gray-600 w-12 text-right">{{ round($projProgress) }}%</span>
</div>
</td>
<td class="px-4 py-3 text-right">
<a href="{{ route('projects.dashboard', $proj->project_id) }}" 
   class="text-primary hover:text-indigo-700 text-sm font-medium">
View →
</a>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@else
<div class="py-12 text-center">
<i class="ri-folder-open-line text-6xl text-gray-300 mb-3"></i>
<p class="text-gray-500 font-medium">No projects found</p>
<p class="text-gray-400 text-sm mt-1">Import projects from Excel to get started</p>
</div>
@endif
</div>
<!-- System Statistics -->
<div class="col-span-12 lg:col-span-6 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">System Statistics</h2>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
<div class="flex items-center justify-between mb-2">
<div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
<i class="ri-user-line text-blue-600 text-xl"></i>
</div>
</div>
<div class="text-2xl font-extrabold text-blue-900">{{ $stats['total_clients'] ?? 0 }}</div>
<div class="text-sm text-blue-700 font-medium">Total Clients</div>
</div>

<div class="bg-green-50 p-4 rounded-lg border border-green-200">
<div class="flex items-center justify-between mb-2">
<div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
<i class="ri-file-list-3-line text-green-600 text-xl"></i>
</div>
</div>
<div class="text-2xl font-extrabold text-green-900">{{ $stats['total_files'] ?? 0 }}</div>
<div class="text-sm text-green-700 font-medium">Total Files</div>
</div>

<div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
<div class="flex items-center justify-between mb-2">
<div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
<i class="ri-stack-line text-purple-600 text-xl"></i>
</div>
</div>
<div class="text-2xl font-extrabold text-purple-900">{{ $stats['total_materials'] ?? 0 }}</div>
<div class="text-sm text-purple-700 font-medium">Materials</div>
</div>

<div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
<div class="flex items-center justify-between mb-2">
<div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
<i class="ri-money-dollar-circle-line text-orange-600 text-xl"></i>
</div>
</div>
<div class="text-2xl font-extrabold text-orange-900">{{ round($stats['payment_progress'] ?? 0) }}%</div>
<div class="text-sm text-orange-700 font-medium">Payment Progress</div>
</div>
</div>
</div>
<!-- Quick Access Section -->
<div class="col-span-12 lg:col-span-6 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Quick Access</h2>
</div>
<div class="grid grid-cols-2 gap-4">
<a href="{{ route('projects.index') }}" class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-blue-600">
<i class="ri-folder-open-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">All Projects</div>
<div class="text-xs text-gray-500">{{ $stats['total_projects'] ?? 0 }} projects</div>
</div>
</div>
</a>
<a href="{{ route('clients.index') }}" class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-purple-600">
<i class="ri-user-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Clients</div>
<div class="text-xs text-gray-500">{{ $stats['total_clients'] ?? 0 }} clients</div>
</div>
</div>
</a>
<a href="{{ route('finance.overview') }}" class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-green-600">
<i class="ri-money-dollar-circle-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Finance Overview</div>
<div class="text-xs text-gray-500">RM {{ number_format($stats['total_paid'] ?? 0, 0) }} paid</div>
</div>
</div>
</a>
<a href="{{ route('inventory') }}" class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-orange-600">
<i class="ri-stack-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Materials</div>
<div class="text-xs text-gray-500">{{ $stats['total_materials'] ?? 0 }} items</div>
</div>
</div>
</a>
<a href="{{ route('status-summary.index') }}" class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-indigo-600">
<i class="ri-bar-chart-2-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Status Summary</div>
<div class="text-xs text-gray-500">View reports</div>
</div>
</div>
</a>
<a href="{{ route('data-import.index') }}" class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-teal-600">
<i class="ri-upload-cloud-2-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Data Import</div>
<div class="text-xs text-gray-500">Import Excel</div>
</div>
</div>
</a>
</div>
</div>
</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
// Wait a bit to ensure DOM is fully ready
setTimeout(function() {
// Weekly Progress Chart
const weeklyProgressChartEl = document.getElementById('weeklyProgressChart');
if (!weeklyProgressChartEl) {
    console.error('Weekly Progress Chart element not found');
    return;
}
const weeklyProgressChart = echarts.init(weeklyProgressChartEl);
const weeklyProgressOption = {
animation: false,
tooltip: {
trigger: 'axis',
backgroundColor: 'rgba(255, 255, 255, 0.9)',
borderColor: '#e5e7eb',
borderWidth: 1,
textStyle: {
color: '#1f2937'
}
},
grid: {
top: 10,
right: 10,
bottom: 20,
left: 40
},
xAxis: {
type: 'category',
data: @json($weeklyLabels),
axisLine: {
lineStyle: {
color: '#e5e7eb'
}
},
axisLabel: {
color: '#1f2937'
}
},
yAxis: {
type: 'value',
min: 0,
max: 100,
axisLine: {
show: false
},
axisLabel: {
color: '#1f2937',
formatter: '{value}%'
},
splitLine: {
lineStyle: {
color: '#f3f4f6'
}
}
},
series: [
{
name: 'Planned',
type: 'line',
smooth: true,
symbol: 'none',
data: @json($weeklyPlanned),
lineStyle: {
color: 'rgba(141, 211, 199, 1)',
width: 3
},
areaStyle: {
color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
{ offset: 0, color: 'rgba(141, 211, 199, 0.3)' },
{ offset: 1, color: 'rgba(141, 211, 199, 0.1)' }
])
}
},
{
name: 'Actual',
type: 'line',
smooth: true,
symbol: 'none',
data: @json($weeklyActual),
lineStyle: {
color: 'rgba(87, 181, 231, 1)',
width: 3
},
areaStyle: {
color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
{ offset: 0, color: 'rgba(87, 181, 231, 0.3)' },
{ offset: 1, color: 'rgba(87, 181, 231, 0.1)' }
])
}
}
]
};
weeklyProgressChart.setOption(weeklyProgressOption);
// Task Status Chart
const taskStatusChartEl = document.getElementById('taskStatusChart');
if (!taskStatusChartEl) {
    console.error('Task Status Chart element not found');
    return;
}
const taskStatusChart = echarts.init(taskStatusChartEl);
const taskStatusOption = {
animation: false,
tooltip: {
trigger: 'item',
backgroundColor: 'rgba(255, 255, 255, 0.9)',
borderColor: '#e5e7eb',
borderWidth: 1,
textStyle: {
color: '#1f2937'
}
},
legend: {
bottom: '0%',
left: 'center',
itemWidth: 12,
itemHeight: 12,
textStyle: {
color: '#1f2937'
}
},
series: [
{
name: 'Task Status',
type: 'pie',
radius: ['40%', '70%'],
center: ['50%', '45%'],
avoidLabelOverlap: false,
itemStyle: {
borderRadius: 6,
borderColor: '#fff',
borderWidth: 2
},
label: {
show: false
},
emphasis: {
label: {
show: false
}
},
labelLine: {
show: false
},
data: [
{ value: {{ $chartData['task_status']['completed'] ?? 0 }}, name: 'Completed', itemStyle: { color: 'rgba(87, 181, 231, 1)' } },
{ value: {{ $chartData['task_status']['in_progress'] ?? 0 }}, name: 'In Progress', itemStyle: { color: 'rgba(141, 211, 199, 1)' } },
{ value: {{ $chartData['task_status']['at_risk'] ?? 0 }}, name: 'At Risk', itemStyle: { color: 'rgba(251, 191, 114, 1)' } },
{ value: {{ $chartData['task_status']['delayed'] ?? 0 }}, name: 'Delayed', itemStyle: { color: 'rgba(252, 141, 98, 1)' } }
]
}
]
};
taskStatusChart.setOption(taskStatusOption);
// Resize charts when window size changes
window.addEventListener('resize', function() {
if (weeklyProgressChart) weeklyProgressChart.resize();
if (taskStatusChart) taskStatusChart.resize();
});
}, 100);
});
</script>
</body>
</html>
</html>
</html>