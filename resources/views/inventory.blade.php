<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Project Material Tracker</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}};</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<style>
body { font-family: 'Inter', sans-serif; }
.custom-input { 
  border: 1.5px solid #e5e7eb; 
  transition: all 0.2s;
}
.custom-input:focus { 
  outline: none; 
  box-shadow: 0 0 0 3px rgba(79,70,229,0.1); 
  border-color: #4f46e5; 
}
.scrollbar-default::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
.scrollbar-default::-webkit-scrollbar-track {
    background: #f3f4f6;
    border-radius: 4px;
}
.scrollbar-default::-webkit-scrollbar-thumb {
    background: #9ca3af;
    border-radius: 4px;
}
.scrollbar-default::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}
th, td {
    white-space: nowrap;
}
table tbody tr {
  transition: background-color 0.15s;
}
table tbody tr:hover {
  background-color: #f9fafb;
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}
.material-tab {
    transition: all 0.2s;
}
.material-tab.active {
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
    color: white;
}
.sticky-header {
    position: sticky;
    top: 0;
    z-index: 15;
    background: white;
}
</style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
@include('partials.navigation')

<!-- Header with Search and Filters -->
<div class="bg-white border-b shadow-sm">
<div class="container mx-auto px-6 py-6">
<div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
<div>
<h1 class="text-3xl font-extrabold text-gray-900">
    <i class="ri-stack-line mr-2 text-primary"></i>Project Materials
</h1>
<p class="text-gray-600 mt-1">Monitor material requirements across all projects</p>
</div>
<div class="flex items-center gap-3">
    <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg">
        <span class="text-xs text-blue-600 font-medium">{{ $projects->total() }} Projects</span>
    </div>
    <div class="px-4 py-2 bg-purple-50 border border-purple-200 rounded-lg">
        <span class="text-xs text-purple-600 font-medium">{{ count($materialColumns) }} Materials</span>
    </div>
</div>
</div>

<!-- Search and Filters -->
<form method="GET" class="mt-6 flex flex-col md:flex-row gap-3">
<div class="flex-1 relative">
<i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
<input type="text" name="search" value="{{ request('search') }}" 
       placeholder="Search by Client, Project ID..." 
       class="w-full pl-10 pr-4 py-3 rounded-xl custom-input">
</div>
<select name="category" class="px-4 py-3 rounded-xl custom-input">
<option value="">All Categories</option>
@foreach($categories as $category)
<option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
@endforeach
</select>
<button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-900 transition-all shadow-sm">
<i class="ri-filter-3-line mr-2"></i>Filter
</button>
@if(request('search') || request('category'))
<a href="{{ route('inventory') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
<i class="ri-close-line"></i>
</a>
@endif
</form>
</div>
</div>

<main class="container-fluid px-6 py-8">
@php
    // Group materials by category (first word before space or hyphen)
    $materialGroups = [];
    foreach($materialColumns as $code => $label) {
        // Extract category from material name
        $parts = preg_split('/[\s\-]+/', $label, 2);
        $category = $parts[0];
        
        if (!isset($materialGroups[$category])) {
            $materialGroups[$category] = [];
        }
        $materialGroups[$category][$code] = $label;
    }
    
    // Add "All Materials" as first tab
    $allMaterialsGroup = ['All' => $materialColumns];
    $materialGroups = $allMaterialsGroup + $materialGroups;
@endphp

<!-- Material Category Tabs -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
    <div class="p-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
        <h3 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
            <i class="ri-apps-line mr-2"></i>Material Categories
        </h3>
        <div class="flex flex-wrap gap-2">
            @foreach($materialGroups as $groupName => $materials)
            <button type="button" 
                    class="material-tab px-4 py-2 rounded-lg font-medium text-sm border transition-all {{ $loop->first ? 'active' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50' }}"
                    onclick="switchMaterialTab('{{ $groupName }}', this)">
                {{ $groupName }}
                <span class="ml-1.5 text-xs opacity-75">({{ count($materials) }})</span>
            </button>
            @endforeach
        </div>
    </div>
</div>

<!-- Material Search within Tab -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 p-4">
    <div class="relative">
        <i class="ri-search-2-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        <input type="text" 
               id="materialSearch" 
               placeholder="Search materials by name..." 
               class="w-full pl-10 pr-4 py-2.5 rounded-lg custom-input text-sm"
               oninput="filterMaterials(this.value)">
    </div>
    <p class="text-xs text-gray-500 mt-2">
        <i class="ri-information-line"></i> 
        Tip: Use the tabs above to filter by category, then search within the category
    </p>
</div>

<!-- Projects Matrix Tables (One per Tab) -->
@foreach($materialGroups as $groupName => $materials)
<div id="tab-{{ $groupName }}" class="tab-content {{ $loop->first ? 'active' : '' }}">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50">
            <div>
                <h2 class="text-lg font-bold text-gray-800 flex items-center">
                    <i class="ri-layout-grid-line mr-2 text-primary"></i>
                    {{ $groupName }} Materials Matrix
                </h2>
                <p class="text-xs text-gray-600 mt-1">
                    Viewing {{ count($materials) }} material{{ count($materials) > 1 ? 's' : '' }} across {{ $projects->total() }} projects
                </p>
            </div>
        </div>

        <div class="overflow-x-auto scrollbar-default" style="max-height: calc(100vh - 400px);">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="sticky-header">
                    <tr>
                        <!-- Fixed Project Columns -->
                        <th class="py-3 px-4 text-left text-xs font-bold uppercase tracking-wider border-r border-b-2 border-gray-300 bg-gradient-to-b from-gray-50 to-gray-100 text-gray-700 sticky left-0 z-20 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.15)] min-w-[220px]">
                            <div class="flex items-center gap-2">
                                <i class="ri-building-line text-primary"></i>
                                Client / Project
                            </div>
                        </th>
                        <th class="py-3 px-3 text-center text-xs font-bold uppercase tracking-wider border-r border-b-2 border-gray-300 bg-gradient-to-b from-gray-50 to-gray-100 text-gray-700 min-w-[80px]">
                            <i class="ri-tools-line"></i>
                        </th>
                        <th class="py-3 px-4 text-left text-xs font-bold uppercase tracking-wider border-r border-b-2 border-gray-300 bg-gradient-to-b from-gray-50 to-gray-100 text-gray-700 min-w-[100px]">Status</th>
                        <th class="py-3 px-4 text-left text-xs font-bold uppercase tracking-wider border-r border-b-2 border-gray-300 bg-gradient-to-b from-gray-50 to-gray-100 text-gray-700 min-w-[90px]">Capacity</th>
                        <th class="py-3 px-4 text-left text-xs font-bold uppercase tracking-wider border-r border-b-2 border-gray-300 bg-gradient-to-b from-gray-50 to-gray-100 text-gray-700 min-w-[140px]">Module</th>
                        <th class="py-3 px-3 text-center text-xs font-bold uppercase tracking-wider border-r border-b-2 border-gray-300 bg-gradient-to-b from-gray-50 to-gray-100 text-gray-700 min-w-[60px]">Qty</th>
                        <th class="py-3 px-4 text-left text-xs font-bold uppercase tracking-wider border-r border-b-2 border-gray-300 bg-gradient-to-b from-gray-50 to-gray-100 text-gray-700 min-w-[140px]">Inverter</th>

                        <!-- Dynamic Material Columns for this group -->
                        @foreach($materials as $code => $label)
                        <th class="material-column py-3 px-3 text-center text-xs font-bold uppercase tracking-wider border-r border-b-2 border-gray-300 bg-gradient-to-b from-blue-50 to-indigo-50 text-indigo-900 min-w-[100px] group cursor-help" data-material-name="{{ strtolower($label) }}">
                            <div class="flex flex-col items-center justify-center gap-1">
                                <i class="ri-stack-fill text-primary text-sm"></i>
                                <span class="line-clamp-3 leading-tight" title="{{ $label }}">{{ $label }}</span>
                            </div>
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($projects as $project)
                    <tr class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-indigo-50/30 transition-all duration-200 group">
                        <!-- Fixed Project Data -->
                        <td class="py-3 px-4 text-sm border-r border-gray-200 sticky left-0 z-10 bg-white group-hover:bg-blue-50/70 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.08)]">
                            <div class="flex flex-col gap-1">
                                <a href="{{ route('projects.show', $project->project_id) }}" 
                                   class="font-semibold text-gray-900 hover:text-primary transition-colors truncate max-w-[190px] flex items-center gap-1" 
                                   title="{{ $project->client->client_name ?? 'Unknown' }}">
                                    <i class="ri-user-line text-xs opacity-50"></i>
                                    {{ $project->client->client_name ?? 'Unknown Client' }}
                                </a>
                                <span class="text-[10px] text-gray-400 font-mono bg-gray-100 px-1.5 py-0.5 rounded inline-block w-fit">
                                    {{ $project->project_id }}
                                </span>
                            </div>
                        </td>
                        <td class="py-2 px-3 text-center border-r border-gray-200">
                            <a href="{{ route('projects.materials.edit', $project->project_id) }}" 
                               class="inline-flex p-1.5 hover:bg-indigo-50 rounded-lg text-gray-500 hover:text-primary hover:shadow-md transition-all" 
                               title="Edit Materials">
                                <i class="ri-edit-2-line text-base"></i>
                            </a>
                        </td>
                        <td class="py-3 px-4 text-xs border-r border-gray-200">
                            @php
                                $statusColors = [
                                    'Planning' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    'Installation' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'Completed' => 'bg-green-100 text-green-700 border-green-200',
                                ];
                                $statusColor = $statusColors[$project->status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $statusColor }} whitespace-nowrap inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                {{ $project->procurement_status ?? $project->status }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-700 border-r border-gray-200 font-mono font-semibold">
                            {{ $project->pv_system_capacity_kwp > 0 ? $project->pv_system_capacity_kwp . ' kWp' : '-' }}
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-900 border-r border-gray-200 truncate max-w-[140px]" title="{{ $project->module }}">
                            {{ Str::limit($project->module, 25) }}
                        </td>
                        <td class="py-3 px-3 text-xs text-center font-mono font-bold text-gray-700 border-r border-gray-200">
                            {{ $project->module_quantity ?: '-' }}
                        </td>
                        <td class="py-3 px-4 text-xs text-gray-900 border-r border-gray-200 truncate max-w-[140px]" title="{{ $project->inverter }}">
                            {{ Str::limit($project->inverter, 25) }}
                        </td>

                        <!-- Dynamic Material Data -->
                        @foreach($materials as $code => $label)
                        @php
                            $pivotMaterial = $project->materials->firstWhere('code', $code);
                            $qty = $pivotMaterial ? $pivotMaterial->pivot->quantity : 0;
                        @endphp
                        <td class="material-column py-2 px-3 text-center text-xs border-r border-gray-200 {{ $qty > 0 ? 'bg-gradient-to-br from-indigo-50 to-purple-50' : 'bg-gray-50/30' }}" data-material-name="{{ strtolower($label) }}">
                            @if($qty > 0)
                                <span class="inline-flex items-center justify-center min-w-[40px] px-2 py-1 rounded-md font-bold text-indigo-700 bg-indigo-100 border border-indigo-200">
                                    {{ number_format($qty) }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ 7 + count($materials) }}" class="py-16 text-center">
                            <i class="ri-inbox-line text-6xl text-gray-300 mb-3 block"></i>
                            <p class="text-gray-500 font-medium text-lg">No projects found</p>
                            <p class="text-gray-400 text-sm mt-1">Try adjusting your search filters</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endforeach

<!-- Pagination -->
@if($projects->hasPages())
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
    {{ $projects->links() }}
</div>
@endif
</main>

<script>
function switchMaterialTab(groupName, button) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Remove active class from all buttons
    document.querySelectorAll('.material-tab').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.add('bg-white', 'border-gray-200', 'text-gray-700', 'hover:bg-gray-50');
    });
    
    // Show selected tab
    document.getElementById('tab-' + groupName).classList.add('active');
    
    // Add active class to clicked button
    button.classList.add('active');
    button.classList.remove('bg-white', 'border-gray-200', 'text-gray-700', 'hover:bg-gray-50');
    
    // Clear material search
    document.getElementById('materialSearch').value = '';
    filterMaterials('');
}

function filterMaterials(searchTerm) {
    const term = searchTerm.toLowerCase().trim();
    
    // Get all material columns in the currently active tab
    const activeTab = document.querySelector('.tab-content.active');
    const materialColumns = activeTab.querySelectorAll('.material-column');
    
    materialColumns.forEach(col => {
        const materialName = col.getAttribute('data-material-name');
        
        if (term === '' || materialName.includes(term)) {
            col.style.display = '';
        } else {
            col.style.display = 'none';
        }
    });
}
</script>

</body>
</html>
