<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Summary</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<style>
:where([class^="ri-"])::before { content: "\f3c2"; }
body {
font-family: 'Inter', sans-serif;
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
</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')

<!-- Main Content -->
<main class="container mx-auto px-6 py-6">
<!-- Filter Section -->
<div class="bg-white rounded shadow-sm p-6 mb-6">
<h2 class="text-lg font-semibold text-gray-800 mb-4">Filter by Status</h2>
<form method="GET" action="{{ route('status-summary.index') }}" id="filterForm">
<div class="space-y-2">
<div class="flex items-center space-x-2">
<input type="checkbox" id="selectAll" class="custom-checkbox" onchange="toggleAll(this)">
<label for="selectAll" class="text-sm font-medium text-gray-700 cursor-pointer">(Select All)</label>
</div>
@foreach($allStatuses as $status)
<div class="flex items-center space-x-2 ml-6">
<input type="checkbox" name="statuses[]" value="{{ $status }}" id="status_{{ $loop->index }}" class="custom-checkbox status-checkbox" 
@if(in_array($status, $selectedStatuses ?? [])) checked @endif>
<label for="status_{{ $loop->index }}" class="text-sm text-gray-600 cursor-pointer">{{ $status }}</label>
</div>
@endforeach
</div>
<div class="mt-4 flex space-x-2">
<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600 transition-colors">Apply Filter</button>
<a href="{{ route('status-summary.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-button hover:bg-gray-200 transition-colors">Clear</a>
</div>
</form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
@foreach($summary as $categoryName => $data)
<div class="bg-white rounded shadow-sm p-6">
<div class="flex items-center justify-between mb-4">
<h3 class="text-lg font-semibold text-gray-800">{{ $categoryName }}</h3>
</div>
<div class="space-y-3">
<div class="flex items-center justify-between">
<span class="text-sm text-gray-600">Cases to be done</span>
<span class="text-lg font-semibold text-gray-800">{{ $data['to_be_done'] }}</span>
</div>
<div class="flex items-center justify-between">
<span class="text-sm text-gray-600">Cases in progress</span>
<span class="text-lg font-semibold text-yellow-600">{{ $data['in_progress'] }}</span>
</div>
<div class="flex items-center justify-between">
<span class="text-sm text-gray-600">Cases completed</span>
<span class="text-lg font-semibold text-green-600">{{ $data['completed'] }}</span>
</div>
</div>
</div>
@endforeach
</div>

<!-- In Progress List -->
<div class="bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">In Progress Cases</h2>
<span class="text-sm text-gray-500">{{ count($inProgressItems) }} items</span>
</div>
@if(count($inProgressItems) > 0)
<div class="overflow-x-auto custom-scrollbar">
<table class="min-w-full divide-y divide-gray-200">
<thead class="bg-gray-50">
<tr>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project ID</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client ID</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
</tr>
</thead>
<tbody class="bg-white divide-y divide-gray-200">
@foreach($inProgressItems as $item)
<tr class="hover:bg-gray-50">
<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item['project_id'] }}</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item['name'] ?? 'N/A' }}</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['client_id'] }}</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['location'] ?? 'N/A' }}</td>
<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item['category'] }}</td>
<td class="px-6 py-4 whitespace-nowrap">
<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">In Progress</span>
</td>
</tr>
@endforeach
</tbody>
</table>
</div>
@else
<div class="text-center py-12">
<div class="text-gray-400 mb-2">
<i class="ri-inbox-line text-4xl"></i>
</div>
<p class="text-gray-500">No in-progress cases found</p>
</div>
@endif
</div>
</main>

<script>
function toggleAll(checkbox) {
const statusCheckboxes = document.querySelectorAll('.status-checkbox');
statusCheckboxes.forEach(cb => {
cb.checked = checkbox.checked;
});
}
</script>
</body>
</html>

