<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Client Management</title>
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
table tbody tr {
  transition: background-color 0.15s;
}
table tbody tr:hover {
  background-color: #f9fafb;
}
</style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
@include('partials.navigation')

<!-- Header with Search and Actions -->
<div class="bg-white border-b shadow-sm">
<div class="container mx-auto px-6 py-6">
<div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
<div>
<h1 class="text-3xl font-extrabold text-gray-900">Client Management</h1>
<p class="text-gray-600 mt-1">Manage your clients and their information</p>
</div>
@if(auth()->user()->role === 'Project Manager')
<a href="{{ route('clients.create') }}" class="px-6 py-3 bg-gradient-to-r from-primary to-secondary text-white rounded-xl font-semibold hover:shadow-xl transition-all hover:scale-105 flex items-center gap-2">
<i class="ri-add-line text-lg"></i>
Add New Client
</a>
@endif
</div>

<!-- Search and Filters -->
<form method="GET" class="mt-6 flex flex-col md:flex-row gap-3">
<div class="flex-1 relative">
<i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
<input type="text" name="search" value="{{ request('search') }}" 
       placeholder="Search by name, ID, email, or phone..." 
       class="w-full pl-10 pr-4 py-3 rounded-xl custom-input">
</div>
<select name="contract_type" class="px-4 py-3 rounded-xl custom-input">
<option value="">All Contract Types</option>
<option value="Outright" {{ request('contract_type') == 'Outright' ? 'selected' : '' }}>Outright</option>
<option value="PPA" {{ request('contract_type') == 'PPA' ? 'selected' : '' }}>PPA</option>
</select>
<button type="submit" class="px-6 py-3 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-900 transition-all">
<i class="ri-filter-3-line mr-2"></i>Filter
</button>
@if(request('search') || request('contract_type'))
<a href="{{ route('clients.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-all">
<i class="ri-close-line"></i>
</a>
@endif
</form>
</div>
</div>

<main class="container mx-auto px-6 py-8">
<!-- Success/Error Messages -->
@if(session('success'))
<div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm">
<div class="flex items-center">
<i class="ri-checkbox-circle-line text-2xl mr-2"></i>
<span class="font-semibold">{{ session('success') }}</span>
</div>
</div>
@endif

@if(session('error'))
<div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 rounded-lg shadow-sm">
<div class="flex items-center">
<i class="ri-error-warning-line text-2xl mr-2"></i>
<span class="font-semibold">{{ session('error') }}</span>
</div>
</div>
@endif

<!-- Clients Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
<div class="p-6 border-b border-gray-200">
<div class="flex items-center justify-between">
<h2 class="text-lg font-semibold text-gray-800">All Clients</h2>
<span class="text-sm font-medium text-gray-600">{{ $clients->total() }} client(s)</span>
</div>
</div>

<div class="overflow-x-auto">
<table class="min-w-full">
<thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
<tr>
<th class="py-4 px-6 text-left text-sm font-bold text-gray-700">Client ID</th>
<th class="py-4 px-6 text-left text-sm font-bold text-gray-700">Name</th>
<th class="py-4 px-6 text-left text-sm font-bold text-gray-700">Contact</th>
<th class="py-4 px-6 text-left text-sm font-bold text-gray-700">IC Number</th>
<th class="py-4 px-6 text-left text-sm font-bold text-gray-700">Payment Method</th>
<th class="py-4 px-6 text-left text-sm font-bold text-gray-700">Contract Type</th>
<th class="py-4 px-6 text-center text-sm font-bold text-gray-700">Projects</th>
<th class="py-4 px-6 text-right text-sm font-bold text-gray-700">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-gray-100">
@forelse($clients as $client)
<tr class="hover:bg-gray-50 transition-colors">
<td class="py-4 px-6">
<span class="font-semibold text-primary">{{ $client->client_id }}</span>
</td>
<td class="py-4 px-6">
<div class="font-semibold text-gray-900">{{ $client->client_name }}</div>
@if($client->installation_address)
<div class="text-xs text-gray-500 mt-1">
<i class="ri-map-pin-line"></i> {{ Str::limit($client->installation_address, 40) }}
</div>
@endif
</td>
<td class="py-4 px-6">
<div class="text-sm text-gray-700">
@if($client->phone_number)
<div><i class="ri-phone-line text-gray-400"></i> {{ $client->phone_number }}</div>
@endif
@if($client->email_address)
<div class="mt-1"><i class="ri-mail-line text-gray-400"></i> {{ $client->email_address }}</div>
@endif
@if(!$client->phone_number && !$client->email_address)
<span class="text-gray-400">-</span>
@endif
</div>
</td>
<td class="py-4 px-6 text-sm text-gray-700">
{{ $client->ic_number ?? '-' }}
</td>
<td class="py-4 px-6">
@if($client->payment_method)
<span class="inline-flex items-center gap-1 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
<i class="ri-bank-card-line"></i>
{{ $client->payment_method }}
</span>
@else
<span class="text-gray-400">-</span>
@endif
</td>
<td class="py-4 px-6">
@if($client->contract_type)
<span class="inline-flex items-center gap-1 px-3 py-1 {{ $client->contract_type == 'Outright' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }} rounded-full text-xs font-semibold">
<i class="ri-file-text-line"></i>
{{ $client->contract_type }}
</span>
@else
<span class="text-gray-400">-</span>
@endif
</td>
<td class="py-4 px-6 text-center">
<span class="inline-flex items-center justify-center w-8 h-8 bg-indigo-100 text-indigo-800 rounded-full font-bold text-sm">
{{ $client->projects_count }}
</span>
</td>
<td class="py-4 px-6">
<div class="flex items-center justify-end gap-2">
<a href="{{ route('clients.show', $client->client_id) }}" 
   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" 
   title="View Details">
<i class="ri-eye-line text-lg"></i>
</a>
@if(auth()->user()->role === 'Project Manager')
<a href="{{ route('clients.edit', $client->client_id) }}" 
   class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors"
   title="Edit Client">
<i class="ri-edit-line text-lg"></i>
</a>
<form action="{{ route('clients.destroy', $client->client_id) }}" 
      method="POST" 
      class="inline"
      onsubmit="return confirm('Are you sure you want to delete this client?');">
@csrf
@method('DELETE')
<button type="submit" 
        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
        title="Delete Client">
<i class="ri-delete-bin-line text-lg"></i>
</button>
</form>
@endif
</div>
</td>
</tr>
@empty
<tr>
<td colspan="8" class="py-12 text-center">
<i class="ri-user-line text-6xl text-gray-300 mb-3"></i>
<p class="text-gray-500 font-medium">No clients found</p>
<p class="text-gray-400 text-sm mt-1">Start by adding your first client</p>
</td>
</tr>
@endforelse
</tbody>
</table>
</div>

<!-- Pagination -->
@if($clients->hasPages())
<div class="p-6 border-t border-gray-200">
{{ $clients->links() }}
</div>
@endif
</div>
</main>
</body>
</html>

