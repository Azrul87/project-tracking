<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add New Client</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}};</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<style>
* { font-family: 'Inter', sans-serif; }
body { background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%); min-height: 100vh; }
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

<div class="container mx-auto px-6 py-8">
<!-- Header -->
<div class="mb-6">
<div class="flex items-center gap-3 mb-2">
<a href="{{ route('clients.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
<i class="ri-arrow-left-line text-2xl"></i>
</a>
<h1 class="text-3xl font-extrabold text-gray-900">Add New Client</h1>
</div>
<p class="text-gray-600 ml-10">Create a new client record</p>
</div>

@if($errors->any())
<div class="mb-6 p-4 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 text-red-800 rounded-lg shadow-sm">
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

<form action="{{ route('clients.store') }}" method="POST">
@csrf

<div class="bg-white rounded-2xl p-8 shadow-xl">
<h2 class="text-2xl font-bold text-gray-900 mb-6">Client Information</h2>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<!-- Client Name -->
<div class="md:col-span-2">
<label class="block text-sm font-semibold text-gray-700 mb-2">
Client Name <span class="text-red-500">*</span>
</label>
<input type="text" 
       name="client_name" 
       value="{{ old('client_name') }}" 
       required
       class="input-field w-full rounded-xl p-3 focus:outline-none bg-white"
       placeholder="Enter client name">
</div>

<!-- IC Number -->
<div>
<label class="block text-sm font-semibold text-gray-700 mb-2">
IC / Passport Number
</label>
<input type="text" 
       name="ic_number" 
       value="{{ old('ic_number') }}" 
       class="input-field w-full rounded-xl p-3 focus:outline-none bg-white"
       placeholder="Enter IC or passport number">
</div>

<!-- Phone Number -->
<div>
<label class="block text-sm font-semibold text-gray-700 mb-2">
Phone Number
</label>
<input type="text" 
       name="phone_number" 
       value="{{ old('phone_number') }}" 
       class="input-field w-full rounded-xl p-3 focus:outline-none bg-white"
       placeholder="+60 12-345 6789">
</div>

<!-- Email Address -->
<div>
<label class="block text-sm font-semibold text-gray-700 mb-2">
Email Address
</label>
<input type="email" 
       name="email_address" 
       value="{{ old('email_address') }}" 
       class="input-field w-full rounded-xl p-3 focus:outline-none bg-white"
       placeholder="client@example.com">
</div>

<!-- Payment Method -->
<div>
<label class="block text-sm font-semibold text-gray-700 mb-2">
Payment Method
</label>
<select name="payment_method" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
<option value="">Select Payment Method</option>
<option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
<option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
<option value="Cheque" {{ old('payment_method') == 'Cheque' ? 'selected' : '' }}>Cheque</option>
<option value="Loan" {{ old('payment_method') == 'Loan' ? 'selected' : '' }}>Loan</option>
<option value="Credit Card" {{ old('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
<option value="Other" {{ old('payment_method') == 'Other' ? 'selected' : '' }}>Other</option>
</select>
</div>

<!-- Contract Type -->
<div>
<label class="block text-sm font-semibold text-gray-700 mb-2">
Contract Type
</label>
<select name="contract_type" class="input-field w-full rounded-xl p-3 focus:outline-none bg-white">
<option value="">Select Contract Type</option>
<option value="Outright" {{ old('contract_type') == 'Outright' ? 'selected' : '' }}>Outright</option>
<option value="PPA" {{ old('contract_type') == 'PPA' ? 'selected' : '' }}>PPA (Power Purchase Agreement)</option>
</select>
</div>

<!-- Installation Address -->
<div class="md:col-span-2">
<label class="block text-sm font-semibold text-gray-700 mb-2">
Installation Address
</label>
<textarea name="installation_address" 
          rows="3" 
          class="input-field w-full rounded-xl p-3 focus:outline-none bg-white"
          placeholder="Enter full installation address">{{ old('installation_address') }}</textarea>
</div>
</div>
</div>

<!-- Action Buttons -->
<div class="mt-6 flex justify-end gap-3">
<a href="{{ route('clients.index') }}" 
   class="px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-200 rounded-xl hover:bg-gray-300 transition-all">
Cancel
</a>
<button type="submit" 
        class="px-8 py-3 text-sm font-semibold text-white bg-gradient-to-r from-primary to-secondary rounded-xl hover:shadow-2xl transition-all hover:scale-105">
<i class="ri-save-line mr-2"></i>Create Client
</button>
</div>
</form>
</div>
</body>
</html>

