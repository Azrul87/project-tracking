<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Insurance Tracker</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}};</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<style>
::where([class^="ri-"])::before { content: "\f3c2"; }
body { font-family: 'Inter', sans-serif; }
.custom-input { border: 1px solid #e5e7eb; }
.custom-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,0.15); border-color: #4f46e5; }
</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-6 mt-4" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

@if(auth()->user()->role === 'Project Manager')
<div class="bg-white border-b px-6 py-3">
<div class="flex items-center justify-end space-x-3">
<button onclick="toggleForm()" class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600 transition-colors whitespace-nowrap">
    <div class="w-4 h-4 flex items-center justify-center"><i class="ri-add-line"></i></div>
    <span>Add Policy</span>
</button>
</div>
</div>
@endif

<main class="container mx-auto px-6 py-6">
<div id="policyForm" class="bg-white rounded shadow-sm p-6 mb-6" style="display: none;">
<h1 class="text-xl font-semibold text-gray-800 mb-4">Add New Insurance Policy</h1>
<form action="{{ route('insurance-tracker.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
@csrf
<div>
<label class="block text-sm text-gray-600 mb-1">Project <span class="text-red-500">*</span></label>
<select name="project_id" required class="w-full rounded-button p-2 custom-input">
    <option value="">Select Project</option>
    @foreach($projects ?? [] as $project)
        <option value="{{ $project->project_id }}">{{ $project->project_id }} - {{ $project->client->client_name ?? 'N/A' }}</option>
    @endforeach
</select>
@error('project_id')
<p class="text-xs text-red-600 mt-1">{{ $message }}</p>
@enderror
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">Insurance Provider <span class="text-red-500">*</span></label>
<input type="text" name="provider_name" required class="w-full rounded-button p-2 custom-input" placeholder="e.g. AIA, Prudential, MSIG" value="{{ old('provider_name') }}">
@error('provider_name')
<p class="text-xs text-red-600 mt-1">{{ $message }}</p>
@enderror
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">Policy Number</label>
<input type="text" name="policy_number" class="w-full rounded-button p-2 custom-input" placeholder="e.g. POL-987654" value="{{ old('policy_number') }}">
@error('policy_number')
<p class="text-xs text-red-600 mt-1">{{ $message }}</p>
@enderror
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">Date of Policy</label>
<input type="date" name="policy_date" class="w-full rounded-button p-2 custom-input" value="{{ old('policy_date') }}">
@error('policy_date')
<p class="text-xs text-red-600 mt-1">{{ $message }}</p>
@enderror
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">Description</label>
<input type="text" name="description" class="w-full rounded-button p-2 custom-input" placeholder="e.g. 1st Year, Renewal" value="{{ old('description') }}">
@error('description')
<p class="text-xs text-red-600 mt-1">{{ $message }}</p>
@enderror
</div>
<div class="md:col-span-2 lg:col-span-3 flex justify-end gap-2 mt-2">
<button type="button" onclick="toggleForm()" class="px-4 py-2 text-sm font-medium text-gray-700 rounded-button hover:bg-gray-100">Cancel</button>
<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600">Save</button>
</div>
</form>
</div>

<div class="bg-white rounded shadow-sm p-6">
<div class="flex items-center justify-between mb-4">
<h2 class="text-lg font-semibold text-gray-800">Policies</h2>
<form action="{{ route('insurance-tracker.index') }}" method="GET" class="flex items-center gap-2">
<input type="text" name="search" value="{{ request('search') }}" class="w-56 rounded-button p-2 custom-input" placeholder="Search by project, client, or policy">
<button type="submit" class="px-3 py-2 text-sm font-medium text-gray-700 rounded-button hover:bg-gray-100">Search</button>
@if(request('search'))
<a href="{{ route('insurance-tracker.index') }}" class="px-3 py-2 text-sm font-medium text-gray-700 rounded-button hover:bg-gray-100">Clear</a>
@endif
</form>
</div>
<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead>
<tr class="text-left text-gray-600 border-b">
<th class="py-3 pr-4 font-medium">Policy ID</th>
<th class="py-3 pr-4 font-medium">Project No</th>
<th class="py-3 pr-4 font-medium">Client Name</th>
<th class="py-3 pr-4 font-medium">Insurance Provider</th>
<th class="py-3 pr-4 font-medium">Policy Number</th>
<th class="py-3 pr-4 font-medium">Date of Policy</th>
<th class="py-3 pr-4 font-medium">Description</th>
<th class="py-3 pr-4 text-right font-medium">Action</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-100">
@forelse($policies ?? [] as $policy)
<tr class="hover:bg-gray-50">
<td class="py-3 pr-4 text-gray-800 font-medium">{{ $policy->policy_id }}</td>
<td class="py-3 pr-4 text-gray-800">{{ $policy->project->project_id ?? 'N/A' }}</td>
<td class="py-3 pr-4 text-gray-600">{{ $policy->project->client->client_name ?? 'N/A' }}</td>
<td class="py-3 pr-4 text-gray-600">{{ $policy->provider_name }}</td>
<td class="py-3 pr-4 text-gray-600">{{ $policy->policy_number ?? '-' }}</td>
<td class="py-3 pr-4 text-gray-600">{{ $policy->policy_date ? $policy->policy_date->format('Y-m-d') : '-' }}</td>
<td class="py-3 pr-4 text-gray-600">{{ $policy->description ?? '-' }}</td>
<td class="py-3 pr-4 text-right">
@if(auth()->user()->role === 'Project Manager')
<form action="{{ route('insurance-tracker.destroy', $policy->policy_id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this policy?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="px-2 py-1 text-xs text-red-600 hover:text-red-700">Delete</button>
</form>
@endif
</td>
</tr>
@empty
<tr>
<td colspan="8" class="py-4 text-center text-gray-500">No insurance policies found.</td>
</tr>
@endforelse
</tbody>
</table>
</div>

@if(isset($policies) && $policies->hasPages())
<div class="mt-4">
{{ $policies->links() }}
</div>
@endif
</div>
</main>

<script>
function toggleForm() {
    const form = document.getElementById('policyForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('policyForm').style.display = 'block';
    });
@endif
</script>
</body>
</html>

