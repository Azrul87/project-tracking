<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Project</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<style>
:where([class^="ri-"])::before { content: "\f3c2"; }
body { font-family: 'Inter', sans-serif; }
</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')

<main class="container mx-auto px-6 py-6">
<div class="bg-white rounded shadow-sm p-6">
<div class="flex items-center justify-between mb-6">
<h1 class="text-2xl font-semibold text-gray-800">Create New Project</h1>
<a href="{{ route('projects.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-button hover:bg-gray-200 transition-colors">
<i class="ri-arrow-left-line"></i> Back to Projects
</a>
</div>

@if(session('success'))
<div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
{{ session('success') }}
</div>
@endif

@if($errors->any())
<div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
<ul class="list-disc list-inside">
@foreach($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
@csrf

<!-- Basic Information -->
<div class="border-b pb-6">
<h2 class="text-lg font-semibold text-gray-800 mb-4">Project Information</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Project ID</label>
<input type="text" value="{{ App\Models\Project::generateProjectId() }}" readonly class="w-full rounded-button p-2 border border-gray-300 bg-gray-100 text-gray-600 cursor-not-allowed">
<small class="text-gray-500">Auto-generated</small>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Project Name</label>
<input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Client <span class="text-red-500">*</span></label>
<select name="client_id" required class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
<option value="">Select Client</option>
@foreach($clients as $client)
<option value="{{ $client->client_id }}" {{ old('client_id') == $client->client_id ? 'selected' : '' }}>{{ $client->client_name }}</option>
@endforeach
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Sales PIC <span class="text-red-500">*</span></label>
<select name="sales_pic_id" required class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
<option value="">Select Sales PIC</option>
@foreach($users as $user)
<option value="{{ $user->user_id }}" {{ old('sales_pic_id') == $user->user_id ? 'selected' : '' }}>{{ $user->name }}</option>
@endforeach
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
<select name="category" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
<option value="">Select Category</option>
<option value="R-PV" {{ old('category') == 'R-PV' ? 'selected' : '' }}>R-PV</option>
<option value="C&I-PV" {{ old('category') == 'C&I-PV' ? 'selected' : '' }}>C&I-PV</option>
<option value="EV Charger" {{ old('category') == 'EV Charger' ? 'selected' : '' }}>EV Charger</option>
<option value="BESS" {{ old('category') == 'BESS' ? 'selected' : '' }}>BESS</option>
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Scheme</label>
<select name="scheme" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
<option value="">Select Scheme</option>
<option value="NEM" {{ old('scheme') == 'NEM' ? 'selected' : '' }}>NEM</option>
<option value="SELCO" {{ old('scheme') == 'SELCO' ? 'selected' : '' }}>SELCO</option>
<option value="None" {{ old('scheme') == 'None' ? 'selected' : '' }}>None</option>
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
<input type="text" name="location" value="{{ old('location') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
<select name="status" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
<option value="Planning" {{ old('status', 'Planning') == 'Planning' ? 'selected' : '' }}>Planning</option>
<option value="In Progress" {{ old('status') == 'In Progress' ? 'selected' : '' }}>In Progress</option>
<option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
</select>
</div>

<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Project Status</label>
<select name="project_status" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
<option value="">Select Status</option>
<option value="Completed" {{ old('project_status') == 'Completed' ? 'selected' : '' }}>Completed</option>
<option value="Pending GITA Application" {{ old('project_status') == 'Pending GITA Application' ? 'selected' : '' }}>Pending GITA Application</option>
<option value="Pending Meter Change" {{ old('project_status') == 'Pending Meter Change' ? 'selected' : '' }}>Pending Meter Change</option>
<option value="Pending NEM Qouta Approval" {{ old('project_status') == 'Pending NEM Qouta Approval' ? 'selected' : '' }}>Pending NEM Qouta Approval</option>
<option value="Pending NEM Qouta Submission" {{ old('project_status') == 'Pending NEM Qouta Submission' ? 'selected' : '' }}>Pending NEM Qouta Submission</option>
<option value="Pending NEM Welcome Letter" {{ old('project_status') == 'Pending NEM Welcome Letter' ? 'selected' : '' }}>Pending NEM Welcome Letter</option>
<option value="Pending Site Installation" {{ old('project_status') == 'Pending Site Installation' ? 'selected' : '' }}>Pending Site Installation</option>
<option value="Pending ST License Application" {{ old('project_status') == 'Pending ST License Application' ? 'selected' : '' }}>Pending ST License Application</option>
<option value="Pending ST License Approval" {{ old('project_status') == 'Pending ST License Approval' ? 'selected' : '' }}>Pending ST License Approval</option>
</select>
</div>
</div>
</div>

<!-- Modules & Inverter -->
<div class="border-b pb-6">
<h2 class="text-lg font-semibold text-gray-800 mb-4">Modules & Inverter</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Module</label>
<select name="module" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
<option value="">Select Module</option>
@php
$modules = [
    'Astronergy 575Wp',
    'Astronergy 585Wp',
    'Astronergy 590Wp',
    'Astronergy 605Wp',
    'Astronergy 610Wp',
    'Astronergy 620Wp',
    'Jinko Solar 575Wp',
    'Yingli Solar 585Wp',
    'Yingli Solar 620Wp',
];
@endphp
@foreach($modules as $m)
<option value="{{ $m }}" {{ old('module') == $m ? 'selected' : '' }}>{{ $m }}</option>
@endforeach
</select>
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Module Quantity</label>
<input type="number" name="module_quantity" value="{{ old('module_quantity') }}" min="0" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Inverter</label>
<input type="text" name="inverter" value="{{ old('inverter') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
</div>
</div>

<!-- Site Survey & Installation -->
<div class="border-b pb-6">
<h2 class="text-lg font-semibold text-gray-800 mb-4">Site Survey & Installation</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Site Survey Date</label>
<input type="date" name="site_survey_date" value="{{ old('site_survey_date') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Installer</label>
<select name="installer" id="installerSelect" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary" onchange="toggleInstallerOther(this.value)">
<option value="">Select Installer</option>
@php
$installers = ['AR Berkat','Ax Electro','Bioserasi','Completed','PJ Plus','Other'];
@endphp
@foreach($installers as $inst)
<option value="{{ $inst }}" {{ old('installer') == $inst ? 'selected' : '' }}>{{ $inst }}</option>
@endforeach
</select>
</div>
<div id="installerOtherWrapper" class="{{ old('installer') === 'Other' ? '' : 'hidden' }}">
<label class="block text-sm font-medium text-gray-700 mb-1">Other Installer</label>
<input type="text" name="installer_other" value="{{ old('installer_other') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Enter installer name">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Installation Date</label>
<input type="date" name="installation_date" value="{{ old('installation_date') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
</div>
</div>
<!-- Technical Specifications -->
<div class="border-b pb-6">
<h2 class="text-lg font-semibold text-gray-800 mb-4">Technical Specifications</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">PV System Capacity (kWp)</label>
<input type="number" name="pv_system_capacity_kwp" value="{{ old('pv_system_capacity_kwp') }}" step="0.01" min="0" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">EV Charger Capacity</label>
<input type="text" name="ev_charger_capacity" value="{{ old('ev_charger_capacity') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">BESS Capacity</label>
<input type="text" name="bess_capacity" value="{{ old('bess_capacity') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
</div>
</div>

<!-- Financial Information -->
<div class="border-b pb-6">
<h2 class="text-lg font-semibold text-gray-800 mb-4">Financial Information</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Project Value (RM)</label>
<input type="number" name="project_value_rm" value="{{ old('project_value_rm') }}" step="0.01" min="0" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">VO Amount (RM)</label>
<input type="number" name="vo_rm" value="{{ old('vo_rm') }}" step="0.01" min="0" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
<input type="text" name="payment_method" value="{{ old('payment_method') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Contract Type</label>
<input type="text" name="contract_type" value="{{ old('contract_type') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Invoice Status</label>
<input type="text" name="invoice_status" value="{{ old('invoice_status') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
<input type="text" name="payment_status" value="{{ old('payment_status') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Procurement Status</label>
<input type="text" name="procurement_status" value="{{ old('procurement_status') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Closed Date</label>
<input type="date" name="closed_date" value="{{ old('closed_date') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
</div>
</div>

<!-- Warranty & Operations -->
<div class="border-b pb-6">
<h2 class="text-lg font-semibold text-gray-800 mb-4">Warranty & Operations</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Insurance/Warranty</label>
<input type="text" name="insurance_warranty" value="{{ old('insurance_warranty') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">DLP Period</label>
<input type="text" name="dlp_period" value="{{ old('dlp_period') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div>
<label class="block text-sm font-medium text-gray-700 mb-1">Partner</label>
<input type="text" name="partner" value="{{ old('partner') }}" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
</div>
<div class="md:col-span-2">
<label class="block text-sm font-medium text-gray-700 mb-1">OM Details</label>
<textarea name="om_details" rows="3" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('om_details') }}</textarea>
</div>
<div class="md:col-span-2">
<label class="block text-sm font-medium text-gray-700 mb-1">Services Exclusion</label>
<textarea name="services_exclusion" rows="3" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('services_exclusion') }}</textarea>
</div>
<div class="md:col-span-2">
<label class="block text-sm font-medium text-gray-700 mb-1">Additional Remarks</label>
<textarea name="additional_remark" rows="3" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">{{ old('additional_remark') }}</textarea>
</div>
</div>
</div>

<!-- Form Actions -->
<div class="flex justify-end space-x-3">
<a href="{{ route('projects.index') }}" class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-button hover:bg-gray-200 transition-colors">Cancel</a>
<button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600 transition-colors">Create Project</button>
</div>
</form>
</div>
</main>

<script>
function toggleInstallerOther(val) {
    const wrap = document.getElementById('installerOtherWrapper');
    if (!wrap) return;
    if (val === 'Other') {
        wrap.classList.remove('hidden');
    } else {
        wrap.classList.add('hidden');
    }
}
// init state on load
document.addEventListener('DOMContentLoaded', () => {
    toggleInstallerOther(document.getElementById('installerSelect')?.value || '');
});
</script>
</body>
</html>

