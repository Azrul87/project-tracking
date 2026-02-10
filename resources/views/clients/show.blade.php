<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Details - {{ $client->client_name }}</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}};</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<style>
* { font-family: 'Inter', sans-serif; }
body { background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%); min-height: 100vh; }
</style>
</head>
<body>
@include('partials.navigation')

<div class="container mx-auto px-6 py-8">
<!-- Header -->
<div class="mb-6">
<div class="flex items-center gap-3 mb-2">
<a href="{{ route('clients.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
<i class="ri-arrow-left-line text-2xl"></i>
</a>
<h1 class="text-3xl font-extrabold text-gray-900">{{ $client->client_name }}</h1>
</div>
<div class="flex items-center gap-3 ml-10">
<span class="text-sm font-medium text-gray-600">{{ $client->client_id }}</span>
<a href="{{ route('clients.edit', $client->client_id) }}" 
   class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-semibold">
<i class="ri-edit-line mr-1"></i>Edit Client
</a>
</div>
</div>

<!-- Client Information Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
<!-- Contact Info Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
<div class="flex items-center gap-3 mb-4">
<div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
<i class="ri-contacts-line text-2xl text-blue-600"></i>
</div>
<h3 class="font-bold text-gray-800">Contact Information</h3>
</div>
<div class="space-y-3 text-sm">
<div class="flex items-start gap-2">
<i class="ri-phone-line text-gray-400 mt-0.5"></i>
<div>
<div class="text-gray-500 text-xs">Phone</div>
<div class="font-medium text-gray-900">{{ $client->phone_number ?? 'Not provided' }}</div>
</div>
</div>
<div class="flex items-start gap-2">
<i class="ri-mail-line text-gray-400 mt-0.5"></i>
<div>
<div class="text-gray-500 text-xs">Email</div>
<div class="font-medium text-gray-900">{{ $client->email_address ?? 'Not provided' }}</div>
</div>
</div>
<div class="flex items-start gap-2">
<i class="ri-id-card-line text-gray-400 mt-0.5"></i>
<div>
<div class="text-gray-500 text-xs">IC/Passport</div>
<div class="font-medium text-gray-900">{{ $client->ic_number ?? 'Not provided' }}</div>
</div>
</div>
</div>
</div>

<!-- Contract Info Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
<div class="flex items-center gap-3 mb-4">
<div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
<i class="ri-file-text-line text-2xl text-green-600"></i>
</div>
<h3 class="font-bold text-gray-800">Contract Details</h3>
</div>
<div class="space-y-3 text-sm">
<div class="flex items-start gap-2">
<i class="ri-bank-card-line text-gray-400 mt-0.5"></i>
<div>
<div class="text-gray-500 text-xs">Payment Method</div>
<div class="font-medium text-gray-900">{{ $client->payment_method ?? 'Not specified' }}</div>
</div>
</div>
<div class="flex items-start gap-2">
<i class="ri-file-list-3-line text-gray-400 mt-0.5"></i>
<div>
<div class="text-gray-500 text-xs">Contract Type</div>
<div class="font-medium text-gray-900">{{ $client->contract_type ?? 'Not specified' }}</div>
</div>
</div>
</div>
</div>

<!-- Address Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
<div class="flex items-center gap-3 mb-4">
<div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
<i class="ri-map-pin-line text-2xl text-purple-600"></i>
</div>
<h3 class="font-bold text-gray-800">Installation Address</h3>
</div>
<div class="text-sm">
<div class="text-gray-900 leading-relaxed">
{{ $client->installation_address ?? 'No address provided' }}
</div>
</div>
</div>
</div>

<!-- Projects Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
<div class="p-6 border-b border-gray-200">
<div class="flex items-center justify-between">
<div>
<h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
<i class="ri-building-line text-primary"></i>
Projects
</h2>
<p class="text-sm text-gray-600 mt-1">{{ $client->projects->count() }} project(s) associated with this client</p>
</div>
</div>
</div>

@if($client->projects->count() > 0)
<div class="overflow-x-auto">
<table class="min-w-full">
<thead class="bg-gray-50 border-b border-gray-200">
<tr>
<th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Project ID</th>
<th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Name</th>
<th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Category</th>
<th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Location</th>
<th class="py-3 px-6 text-left text-sm font-semibold text-gray-700">Status</th>
<th class="py-3 px-6 text-right text-sm font-semibold text-gray-700">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-100">
@foreach($client->projects as $project)
<tr class="hover:bg-gray-50 transition-colors">
<td class="py-4 px-6">
<span class="font-semibold text-primary">{{ $project->project_id }}</span>
</td>
<td class="py-4 px-6 text-gray-900">
{{ $project->name ?? 'Untitled Project' }}
</td>
<td class="py-4 px-6">
@if($project->category)
<span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
{{ $project->category }}
</span>
@else
<span class="text-gray-400">-</span>
@endif
</td>
<td class="py-4 px-6 text-sm text-gray-600">
{{ $project->location ?? '-' }}
</td>
<td class="py-4 px-6">
@php
$statusColors = [
    'Planning' => 'bg-yellow-100 text-yellow-800',
    'In Progress' => 'bg-blue-100 text-blue-800',
    'Completed' => 'bg-green-100 text-green-800',
];
$color = $statusColors[$project->status] ?? 'bg-gray-100 text-gray-800';
@endphp
<span class="px-3 py-1 {{ $color }} rounded-full text-xs font-semibold">
{{ $project->status }}
</span>
</td>
<td class="py-4 px-6 text-right">
<a href="{{ route('projects.dashboard', $project->project_id) }}" 
   class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary text-white rounded-lg hover:bg-indigo-700 transition-colors text-xs font-semibold">
<i class="ri-eye-line"></i>
View
</a>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@else
<div class="p-12 text-center">
<i class="ri-folder-open-line text-6xl text-gray-300 mb-3"></i>
<p class="text-gray-500 font-medium">No projects found</p>
<p class="text-gray-400 text-sm mt-1">This client doesn't have any projects yet</p>
</div>
@endif
</div>
</div>
</body>
</html>

