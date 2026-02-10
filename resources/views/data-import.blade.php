<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Import - Project Tracking</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}};</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<style>
body { font-family: 'Inter', sans-serif; }
.custom-input { 
  border: 1px solid #e5e7eb; 
  transition: all 0.2s;
}
.custom-input:focus { 
  outline: none; 
  box-shadow: 0 0 0 3px rgba(79,70,229,0.1); 
  border-color: #4f46e5; 
}
</style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
@include('partials.navigation')

<main class="container mx-auto px-6 py-6">
<!-- Supported Fields Info -->
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 rounded-lg p-6 mb-6">
<div class="flex items-start gap-4">
<i class="ri-information-line text-3xl text-blue-600 mt-1"></i>
<div class="flex-1">
<h2 class="text-lg font-bold text-gray-800 mb-3">Supported Import Fields</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
<div>
<h3 class="font-semibold text-gray-700 mb-2">📋 Basic Info</h3>
<ul class="text-gray-600 space-y-1 text-xs">
<li>• Project No/ID</li>
<li>• Client Name</li>
<li>• Sales PIC</li>
<li>• Project Name</li>
<li>• Category</li>
<li>• Scheme</li>
<li>• Location</li>
<li>• <strong>Status</strong> (Planning/In Progress/Completed)</li>
</ul>
<p class="text-xs text-gray-500 mt-2 italic">
Status column: "Status", "General Status", "Project Status", or "Current Status"
</p>
</div>
<div>
<h3 class="font-semibold text-gray-700 mb-2">⚙️ Technical</h3>
<ul class="text-gray-600 space-y-1 text-xs">
<li>• PV System Capacity (kWp)</li>
<li>• Module</li>
<li>• Module Quantity</li>
<li>• Inverter</li>
<li>• EV Charger Capacity</li>
<li>• BESS Capacity</li>
<li>• Installer</li>
<li>• Site Survey Date</li>
<li>• Installation Date</li>
</ul>
</div>
<div>
<h3 class="font-semibold text-gray-700 mb-2">💰 Financial</h3>
<ul class="text-gray-600 space-y-1 text-xs">
<li>• Project Value (RM)</li>
<li>• VO Amount (RM)</li>
<li>• Payment Method</li>
<li>• Contract Type</li>
<li>• Invoice Status</li>
<li>• Payment Status</li>
<li>• Procurement Status</li>
<li>• Closed Date</li>
</ul>
</div>
<div>
<h3 class="font-semibold text-gray-700 mb-2">🔄 Workflow Dates</h3>
<ul class="text-gray-600 space-y-1 text-xs">
<li>• Client Enquiry</li>
<li>• Proposal Submission/Acceptance</li>
<li>• Letter of Award</li>
<li>• NEM Quota Submission/Approval</li>
<li>• ST License Application/Approval</li>
<li>• Material Procurement/Delivery</li>
<li>• Site Mobilization</li>
<li>• System Testing/Commissioning</li>
<li>• NEM Meter Change</li>
<li>• NEMCD Obtained</li>
<li>• System Energize/Training</li>
<li>• Project Handover/Closure</li>
<li>• O&M Workflow Dates</li>
</ul>
</div>
</div>
<div class="mt-4 space-y-2">
<p class="text-xs text-gray-600">
<i class="ri-lightbulb-line"></i> <strong>Tip:</strong> Column names are flexible - the system will try to match similar variations (e.g., "Project No", "Project ID", "Project Number" all work)
</p>
<p class="text-xs text-green-700 bg-green-50 border border-green-200 rounded-lg p-2">
<i class="ri-shield-check-line"></i> <strong>Smart Update:</strong> The system will only update fields that have values in your Excel. Empty cells won't overwrite existing data, so you can safely import partial updates!
</p>
</div>
</div>
</div>
</div>

<div class="bg-white rounded shadow-sm p-6 mb-6">
<h1 class="text-xl font-semibold text-gray-800 mb-4">Upload Excel File</h1>
@if ($errors->any())
<div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm">
    <div class="flex items-start gap-3">
        <i class="ri-error-warning-line text-2xl text-red-600"></i>
        <div>
            <h3 class="font-semibold text-red-800 mb-1">Error</h3>
            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
        </div>
    </div>
</div>
@endif

@if (session('import_success'))
<div class="mb-4 p-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg shadow-sm">
    <div class="flex items-start gap-3">
        <i class="ri-checkbox-circle-line text-3xl text-green-600"></i>
        <div class="flex-1">
            <h3 class="font-bold text-green-800 mb-2 text-lg">Import Successful!</h3>
            <pre class="text-sm text-green-700 whitespace-pre-wrap mb-3">{{ session('import_success') }}</pre>
            @if (session('import_results'))
            @php($results = session('import_results'))
            <div class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-3">
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $results['projects']['imported'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Projects</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $results['payments']['imported'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Payments</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-orange-600">{{ $results['insurance']['imported'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Insurance</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-teal-600">{{ $results['items']['imported'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Items</div>
                </div>
                <div class="bg-white rounded-lg p-3 text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $results['project_items']['imported'] ?? 0 }}</div>
                    <div class="text-xs text-gray-600 mt-1">Project Items</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endif

@if (session('import_error'))
<div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm">
    <div class="flex items-start gap-3">
        <i class="ri-close-circle-line text-2xl text-red-600"></i>
        <div>
            <h3 class="font-semibold text-red-800 mb-1">Import Failed</h3>
            <p class="text-sm text-red-700">{{ session('import_error') }}</p>
        </div>
    </div>
</div>
@endif

@if(in_array(auth()->user()->role ?? '', ['Project Manager', 'Supply Chain', 'Finance']))
<form action="{{ route('data-import.analyze') }}" method="post" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
@csrf
<div class="flex-1 w-full">
<label class="block text-sm font-medium text-gray-700 mb-2">
<i class="ri-file-excel-2-line mr-1"></i>Select Excel File (.xlsx, .xls)
</label>
<input type="file" name="file" accept=".xlsx,.xls" required 
       class="custom-input rounded-lg p-3 w-full border-2 hover:border-primary transition-colors file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-indigo-600 file:cursor-pointer">
</div>
<button type="submit" class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-primary to-secondary rounded-lg hover:shadow-lg transition-all hover:scale-105 flex items-center gap-2 mt-6">
<i class="ri-search-eye-line"></i>
Analyze File
</button>
</form>
@else
<div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
<p class="text-sm text-yellow-800"><i class="ri-lock-line mr-1"></i> You do not have permission to import data. Contact a Project Manager, Supply Chain, or Finance user.</p>
</div>
@endif
</div>

@if (session('import_sheets'))
@php($sheets = session('import_sheets'))
@php($path = session('import_path'))
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
<div class="flex items-center justify-between mb-4">
<div>
<h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
<i class="ri-file-list-3-line text-primary"></i>
Excel Sheets Found
</h2>
<p class="text-sm text-gray-600 mt-1">{{ count($sheets) }} sheet(s) detected in the file</p>
</div>
</div>
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-lg">
<div class="flex items-start gap-2">
<i class="ri-information-line text-yellow-600 text-lg mt-0.5"></i>
<p class="text-sm text-yellow-800"><strong>Important:</strong> Select which sheets to import. Projects must be imported first before related data (payments, insurance, items).</p>
</div>
</div>

<form action="{{ route('data-import.commit') }}" method="post" id="importForm">
@csrf
<input type="hidden" name="path" value="{{ $path }}">

<div class="space-y-4">
@foreach($sheets as $sheetName => $sheetData)
<div class="border-2 border-gray-200 rounded-xl p-5 hover:border-primary transition-all bg-gray-50">
<div class="flex items-start justify-between mb-3">
<div class="flex items-center space-x-3">
<input type="checkbox" 
       name="sheets[]" 
       value="{{ $sheetName }}" 
       id="sheet_{{ $loop->index }}"
       class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-2 focus:ring-primary cursor-pointer"
       @if(in_array($sheetName, ['ECN Master Project Tracker', 'ECN Master Project Tracker new'])) checked @endif>
<label for="sheet_{{ $loop->index }}" class="font-bold text-gray-800 cursor-pointer text-lg flex items-center gap-2">
    <i class="ri-file-excel-line text-green-600"></i>
    {{ $sheetName }}
</label>
</div>
<div class="flex items-center gap-3 text-sm">
<span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-medium">
    <i class="ri-list-check text-xs"></i> {{ $sheetData['rows'] }} rows
</span>
<span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full font-medium">
    <i class="ri-layout-column-line text-xs"></i> {{ $sheetData['columns'] }} cols
</span>
</div>
</div>

<div class="overflow-x-auto mt-4 bg-white rounded-lg border border-gray-200">
<table class="min-w-full text-xs">
<thead class="bg-gradient-to-r from-gray-50 to-gray-100">
<tr>
@foreach($sheetData['headers'] as $h)
<th class="py-3 px-3 text-left text-gray-700 font-semibold border-b-2 border-gray-200 whitespace-nowrap">{{ (string) $h }}</th>
@endforeach
</tr>
</thead>
<tbody class="divide-y divide-gray-100">
@foreach($sheetData['preview'] as $r)
<tr class="hover:bg-blue-50 transition-colors">
@foreach($r as $cell)
<td class="py-2 px-3 text-gray-700 whitespace-nowrap">{{ is_scalar($cell) ? (string) $cell : '' }}</td>
@endforeach
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
@endforeach
</div>

<div class="mt-8 flex flex-col sm:flex-row items-center justify-between gap-4 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-200">
<div class="text-sm">
    <div class="flex items-center gap-2 text-gray-700 mb-2">
        <i class="ri-arrow-right-line text-primary"></i>
        <strong>Import Order:</strong>
    </div>
    <div class="flex flex-wrap items-center gap-2 text-xs text-gray-600">
        <span class="bg-white px-3 py-1 rounded-full font-medium">1. Projects</span>
        <i class="ri-arrow-right-s-line"></i>
        <span class="bg-white px-3 py-1 rounded-full font-medium">2. Items</span>
        <i class="ri-arrow-right-s-line"></i>
        <span class="bg-white px-3 py-1 rounded-full font-medium">3. Payments/Insurance</span>
        <i class="ri-arrow-right-s-line"></i>
        <span class="bg-white px-3 py-1 rounded-full font-medium">4. Project Items</span>
    </div>
</div>
<button type="submit" class="px-8 py-3 text-sm font-bold text-white bg-gradient-to-r from-primary to-secondary rounded-lg hover:shadow-xl transition-all hover:scale-105 flex items-center gap-2 whitespace-nowrap">
    <i class="ri-upload-cloud-2-line text-lg"></i>
    Import Selected Sheets
</button>
</div>
</form>
</div>
@endif

</main>
</body>
</html>
