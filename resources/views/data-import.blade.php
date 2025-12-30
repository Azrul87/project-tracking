<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Import</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}};</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<style>
body { font-family: 'Inter', sans-serif; }
.custom-input { border: 1px solid #e5e7eb; }
.custom-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,0.15); border-color: #4f46e5; }
</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')

<main class="container mx-auto px-6 py-6">
<div class="bg-white rounded shadow-sm p-6 mb-6">
<h1 class="text-xl font-semibold text-gray-800 mb-4">Upload Excel File</h1>
@if ($errors->any())
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
    {{ $errors->first() }}
</div>
@endif

@if (session('import_success'))
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded">
    <h3 class="font-semibold text-green-800 mb-2">Import Successful!</h3>
    <pre class="text-sm text-green-700 whitespace-pre-wrap">{{ session('import_success') }}</pre>
    @if (session('import_results'))
    @php($results = session('import_results'))
    <div class="mt-3 text-sm">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
            <div>Projects: <span class="font-semibold">{{ $results['projects']['imported'] ?? 0 }}</span></div>
            <div>Payments: <span class="font-semibold">{{ $results['payments']['imported'] ?? 0 }}</span></div>
            <div>Insurance: <span class="font-semibold">{{ $results['insurance']['imported'] ?? 0 }}</span></div>
            <div>Items: <span class="font-semibold">{{ $results['items']['imported'] ?? 0 }}</span></div>
            <div>Project Items: <span class="font-semibold">{{ $results['project_items']['imported'] ?? 0 }}</span></div>
        </div>
    </div>
    @endif
</div>
@endif

@if (session('import_error'))
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded text-sm text-red-700">
    {{ session('import_error') }}
</div>
@endif

<form action="{{ route('data-import.analyze') }}" method="post" enctype="multipart/form-data" class="flex items-center gap-3">
@csrf
<input type="file" name="file" accept=".xlsx,.xls" required class="custom-input rounded-button p-2">
<button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600">Analyze File</button>
</form>
</div>

@if (session('import_sheets'))
@php($sheets = session('import_sheets'))
@php($path = session('import_path'))
<div class="bg-white rounded shadow-sm p-6 mb-6">
<h2 class="text-lg font-semibold text-gray-800 mb-4">Excel Sheets Found ({{ count($sheets) }})</h2>
<p class="text-sm text-gray-600 mb-4">Select which sheets to import. Projects must be imported first for related data.</p>

<form action="{{ route('data-import.commit') }}" method="post" id="importForm">
@csrf
<input type="hidden" name="path" value="{{ $path }}">

<div class="space-y-6">
@foreach($sheets as $sheetName => $sheetData)
<div class="border border-gray-200 rounded p-4">
<div class="flex items-start justify-between mb-3">
<div class="flex items-center space-x-3">
<input type="checkbox" 
       name="sheets[]" 
       value="{{ $sheetName }}" 
       id="sheet_{{ $loop->index }}"
       class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary"
       @if(in_array($sheetName, ['ECN Master Project Tracker', 'ECN Master Project Tracker new'])) checked @endif>
<label for="sheet_{{ $loop->index }}" class="font-semibold text-gray-800 cursor-pointer">
    {{ $sheetName }}
</label>
</div>
<div class="text-sm text-gray-600">
    {{ $sheetData['rows'] }} rows • {{ $sheetData['columns'] }} columns
</div>
</div>

<div class="overflow-x-auto mt-3">
<table class="min-w-full text-xs border border-gray-200">
<thead class="bg-gray-50">
<tr>
@foreach($sheetData['headers'] as $h)
<th class="py-2 px-2 text-left text-gray-600 border-b">{{ (string) $h }}</th>
@endforeach
</tr>
</thead>
<tbody class="divide-y divide-gray-100">
@foreach($sheetData['preview'] as $r)
<tr>
@foreach($r as $cell)
<td class="py-1 px-2 text-gray-800 border-b">{{ is_scalar($cell) ? (string) $cell : '' }}</td>
@endforeach
</tr>
@endforeach
</tbody>
</table>
</div>
</div>
@endforeach
</div>

<div class="mt-6 flex items-center justify-between">
<div class="text-sm text-gray-600">
    <strong>Note:</strong> Import order: Projects → Items → Payments/Insurance → Project Items
</div>
<button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600">
    Import Selected Sheets
</button>
</div>
</form>
</div>
@endif

</main>
</body>
</html>
