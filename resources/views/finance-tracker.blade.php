<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Finance Tracker</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}};</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
body { font-family: 'Inter', sans-serif; }
.custom-input { border: 1px solid #e5e7eb; }
.custom-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,0.15); border-color: #4f46e5; }
.custom-input:disabled { background-color: #f3f4f6; cursor: not-allowed; }
.status-badge { padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
.status-paid { background-color: #dcfce7; color: #166534; }
.status-pending { background-color: #fef3c7; color: #92400e; }
.status-overdue { background-color: #fecaca; color: #991b1b; }
</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')

@if(session('success'))
<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-6 mt-4" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
</div>
@endif

<div class="bg-white border-b px-6 py-3">
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<label class="text-sm text-gray-600">Select Project:</label>
<select id="projectSelect" class="rounded-button p-2 custom-input w-64">
<option value="">-- Select a project --</option>
@foreach($projects as $proj)
<option value="{{ $proj->project_id }}" {{ $project && $project->project_id === $proj->project_id ? 'selected' : '' }}>
{{ $proj->project_id }} - {{ $proj->client->client_name ?? 'N/A' }}
</option>
@endforeach
</select>
</div>
<div class="flex items-center space-x-3">
@if($canEdit)
<button id="resetAll" class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 rounded-button hover:bg-gray-100 transition-colors whitespace-nowrap">
<div class="w-4 h-4 flex items-center justify-center"><i class="ri-refresh-line"></i></div>
<span>Reset</span>
</button>
<button id="saveAll" class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600 transition-colors whitespace-nowrap">
<div class="w-4 h-4 flex items-center justify-center"><i class="ri-save-3-line"></i></div>
<span>Save</span>
</button>
@endif
</div>
</div>
</div>

<main class="container mx-auto px-6 py-6">
<form id="financeForm" method="POST" action="{{ route('finance.tracker.store') }}">
@csrf
<input type="hidden" id="project_id" name="project_id" value="{{ $project ? $project->project_id : '' }}">

<div class="bg-white rounded shadow-sm p-6 mb-6">
<h1 class="text-xl font-semibold text-gray-800 mb-4">Finance Tracker</h1>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
<div>
<label class="block text-sm text-gray-600 mb-1">Project No</label>
<input id="projectNo" type="text" class="w-full rounded-button p-2 custom-input" value="{{ $project ? $project->project_id : '' }}" readonly>
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">Client Name</label>
<input id="clientName" type="text" class="w-full rounded-button p-2 custom-input" value="{{ $project && $project->client ? $project->client->client_name : '' }}" readonly>
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">Project Value (RM)</label>
<input id="projectValue" name="project_value_rm" type="number" min="0" step="0.01" class="w-full rounded-button p-2 custom-input" value="{{ $project ? $project->project_value_rm : '' }}" {{ !$canEdit ? 'disabled' : '' }} placeholder="0.00">
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">VO (Variation Order) Amount (RM)</label>
<input id="voAmount" name="vo_rm" type="number" min="0" step="0.01" class="w-full rounded-button p-2 custom-input" value="{{ $project ? $project->vo_rm : '' }}" {{ !$canEdit ? 'disabled' : '' }} placeholder="0.00">
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">Sales PIC</label>
<input id="salesPic" type="text" class="w-full rounded-button p-2 custom-input" value="{{ $project && $project->salesPic ? $project->salesPic->name : '' }}" readonly>
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">Number of Payments</label>
<select id="numPayments" class="w-full rounded-button p-2 custom-input" {{ !$canEdit ? 'disabled' : '' }}>
<option value="1">1</option>
<option value="2">2</option>
<option value="3" selected>3</option>
<option value="4">4</option>
<option value="5">5</option>
</select>
</div>
</div>
</div>

<div class="bg-white rounded shadow-sm p-6 mb-6">
<div class="flex items-center justify-between mb-4">
<h2 class="text-lg font-semibold text-gray-800">Invoices & Payments</h2>
</div>

<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead>
<tr class="text-left text-gray-600">
<th class="py-2 pr-4">Phase</th>
<th class="py-2 pr-4">Invoice Date</th>
<th class="py-2 pr-4">Invoice Amount</th>
<th class="py-2 pr-4">Payment Date</th>
<th class="py-2 pr-4">Payment Amount</th>
<th class="py-2 pr-4">Invoice Status</th>
<th class="py-2 pr-4">Payment Status</th>
@if($canEdit)
<th class="py-2 pr-4">Actions</th>
@endif
</tr>
</thead>
<tbody id="paymentsBody" class="divide-y divide-gray-100"></tbody>
</table>
</div>
</div>

<div class="bg-white rounded shadow-sm p-6 mb-6">
<h2 class="text-lg font-semibold text-gray-800 mb-4">VO (Variation Order)</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
<div>
<label class="block text-sm text-gray-600 mb-1">VO Invoice Date</label>
<input id="voInvoiceDate" name="vo_invoice_date" type="date" class="w-full rounded-button p-2 custom-input" value="{{ $project ? ($project->payments->where('description', 'like', '%VO%')->first()->invoice_date ?? '') : '' }}" {{ !$canEdit ? 'disabled' : '' }}>
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">VO Invoice Amount (RM)</label>
<input id="voInvoiceAmount" name="vo_invoice_amount" type="number" min="0" step="0.01" class="w-full rounded-button p-2 custom-input" value="{{ $project ? ($project->payments->where('description', 'like', '%VO%')->first()->invoice_amount ?? '') : '' }}" {{ !$canEdit ? 'disabled' : '' }} placeholder="0.00">
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">VO Payment Date</label>
<input id="voPaymentDate" name="vo_payment_date" type="date" class="w-full rounded-button p-2 custom-input" value="{{ $project ? ($project->payments->where('description', 'like', '%VO%')->first()->payment_date ?? '') : '' }}" {{ !$canEdit ? 'disabled' : '' }}>
</div>
<div>
<label class="block text-sm text-gray-600 mb-1">VO Payment Amount (RM)</label>
<input id="voPaymentAmount" name="vo_payment_amount" type="number" min="0" step="0.01" class="w-full rounded-button p-2 custom-input" value="{{ $project ? ($project->payments->where('description', 'like', '%VO%')->first()->payment_amount ?? '') : '' }}" {{ !$canEdit ? 'disabled' : '' }} placeholder="0.00">
</div>
</div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
<div class="bg-white rounded shadow-sm p-6">
<div class="text-sm text-gray-600">Total Invoiced</div>
<div id="totalInvoiced" class="text-2xl font-bold text-gray-900">RM 0.00</div>
</div>
<div class="bg-white rounded shadow-sm p-6">
<div class="text-sm text-gray-600">Total Paid</div>
<div id="totalPaid" class="text-2xl font-bold text-gray-900">RM 0.00</div>
</div>
<div class="bg-white rounded shadow-sm p-6">
<div class="text-sm text-gray-600">Outstanding</div>
<div id="outstanding" class="text-2xl font-bold text-gray-900">RM 0.00</div>
</div>
</div>
</form>
</main>

<script>
const fmt = (n)=> (isNaN(n)?0:n).toLocaleString('en-MY',{style:'currency',currency:'MYR'});
const canEdit = {{ $canEdit ? 'true' : 'false' }};
const existingPayments = @json($paymentsData ?? []);

function renderPaymentRows() {
  const body = document.getElementById('paymentsBody');
  const count = parseInt(document.getElementById('numPayments').value, 10);
  body.innerHTML = '';
  
  // Filter out VO payments
  const regularPayments = existingPayments.filter(p => !p.description.toLowerCase().includes('vo'));
  
  for (let i = 1; i <= count; i++) {
    const existingPayment = regularPayments[i - 1] || null;
    const paymentId = existingPayment ? existingPayment.payment_id : '';
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="py-2 pr-4 text-gray-800">${i} ${i===1?'st':i===2?'nd':i===3?'rd':'th'} Payment</td>
      <td class="py-2 pr-4">
        <input type="date" name="payments[${i-1}][invoice_date]" class="w-full rounded-button p-2 custom-input invoice-date" data-idx="${i}" value="${existingPayment?.invoice_date || ''}" ${!canEdit ? 'disabled' : ''}>
        ${paymentId ? `<input type="hidden" name="payments[${i-1}][payment_id]" value="${paymentId}">` : ''}
      </td>
      <td class="py-2 pr-4">
        <input type="number" min="0" step="0.01" name="payments[${i-1}][invoice_amount]" class="w-full rounded-button p-2 custom-input invoice-amount" data-idx="${i}" value="${existingPayment?.invoice_amount || ''}" ${!canEdit ? 'disabled' : ''} placeholder="0.00">
      </td>
      <td class="py-2 pr-4">
        <input type="date" name="payments[${i-1}][payment_date]" class="w-full rounded-button p-2 custom-input payment-date" data-idx="${i}" value="${existingPayment?.payment_date || ''}" ${!canEdit ? 'disabled' : ''}>
      </td>
      <td class="py-2 pr-4">
        <input type="number" min="0" step="0.01" name="payments[${i-1}][payment_amount]" class="w-full rounded-button p-2 custom-input payment-amount" data-idx="${i}" value="${existingPayment?.payment_amount || ''}" ${!canEdit ? 'disabled' : ''} placeholder="0.00">
      </td>
      <td class="py-2 pr-4">
        <input type="hidden" name="payments[${i-1}][description]" value="${i} ${i===1?'st':i===2?'nd':i===3?'rd':'th'} Payment">
        <span id="invoiceStatus-${i}" class="status-badge status-pending">pending ${i===1?'1st':i===2?'2nd':i===3?'3rd':i+'th'} invoice</span>
      </td>
      <td class="py-2 pr-4">
        <span id="paymentStatus-${i}" class="status-badge status-pending">pending ${i===1?'1st':i===2?'2nd':i===3?'3rd':i+'th'} payment</span>
      </td>
      ${canEdit && paymentId ? `
      <td class="py-2 pr-4">
        <form action="/finance-tracker/payment/${paymentId}" method="POST" onsubmit="return confirm('Delete this payment?');" class="inline">
          <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <input type="hidden" name="_method" value="DELETE">
          <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
            <i class="ri-delete-bin-line"></i>
          </button>
        </form>
      </td>
      ` : canEdit ? '<td class="py-2 pr-4"></td>' : ''}
    `;
    body.appendChild(tr);
  }
  bindDynamicInputs();
  updateTotalsAndStatuses();
}

function updateTotalsAndStatuses() {
  const count = parseInt(document.getElementById('numPayments').value, 10);
  let totalInv = 0; let totalPaid = 0;
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  
  for (let i = 1; i <= count; i++) {
    const invAmt = parseFloat(document.querySelector(`.invoice-amount[data-idx="${i}"]`)?.value || '0');
    const payAmt = parseFloat(document.querySelector(`.payment-amount[data-idx="${i}"]`)?.value || '0');
    const invDateStr = document.querySelector(`.invoice-date[data-idx="${i}"]`)?.value;
    totalInv += invAmt;
    totalPaid += payAmt;

    const invStatusEl = document.getElementById(`invoiceStatus-${i}`);
    const payStatusEl = document.getElementById(`paymentStatus-${i}`);

    if (invAmt > 0) {
      invStatusEl.textContent = `${i===1?'1st':i===2?'2nd':i===3?'3rd':i+'th'} invoice`;
      invStatusEl.className = 'status-badge ' + (invDateStr ? 'status-paid' : 'status-pending');
    } else {
      invStatusEl.textContent = `pending ${i===1?'1st':i===2?'2nd':i===3?'3rd':i+'th'} invoice`;
      invStatusEl.className = 'status-badge status-pending';
    }

    if (payAmt > 0) {
      payStatusEl.textContent = `${i===1?'1st':i===2?'2nd':i===3?'3rd':i+'th'} payment received`;
      payStatusEl.className = 'status-badge status-paid';
    } else {
      if (invDateStr) {
        const invDate = new Date(invDateStr);
        invDate.setHours(0, 0, 0, 0);
        const overdue = invDate < today;
        payStatusEl.textContent = `pending ${i===1?'1st':i===2?'2nd':i===3?'3rd':i+'th'} payment`;
        payStatusEl.className = 'status-badge ' + (overdue ? 'status-overdue' : 'status-pending');
      } else {
        payStatusEl.textContent = `pending ${i===1?'1st':i===2?'2nd':i===3?'3rd':i+'th'} payment`;
        payStatusEl.className = 'status-badge status-pending';
      }
    }
  }

  const voInv = parseFloat(document.getElementById('voInvoiceAmount').value || '0');
  const voPay = parseFloat(document.getElementById('voPaymentAmount').value || '0');
  totalInv += voInv;
  totalPaid += voPay;

  const projectValue = parseFloat(document.getElementById('projectValue').value || '0');
  const voAmount = parseFloat(document.getElementById('voAmount').value || '0');
  const contractTotal = projectValue + voAmount;

  document.getElementById('totalInvoiced').textContent = fmt(totalInv);
  document.getElementById('totalPaid').textContent = fmt(totalPaid);
  document.getElementById('outstanding').textContent = fmt(Math.max(contractTotal - totalPaid, 0));
}

function bindDynamicInputs() {
  document.querySelectorAll('.invoice-date, .invoice-amount, .payment-date, .payment-amount').forEach(el => {
    el.addEventListener('input', updateTotalsAndStatuses);
    el.addEventListener('change', updateTotalsAndStatuses);
  });
}

document.addEventListener('DOMContentLoaded', () => {
  // Initialize Select2 for project dropdown
  $('#projectSelect').select2({
    placeholder: 'Search and select a project',
    allowClear: true,
    width: '256px'
  });

  // Handle project selection with Select2 - must be after initialization
  $('#projectSelect').on('change', function() {
    const projectId = this.value;
    if (projectId) {
      window.location.href = '{{ route("finance.tracker.project", ":id") }}'.replace(':id', projectId);
    } else {
      window.location.href = '{{ route("finance.tracker") }}';
    }
  });

  renderPaymentRows();

  document.getElementById('numPayments').addEventListener('change', () => {
    renderPaymentRows();
  });

  ['projectValue','voAmount','voInvoiceAmount','voPaymentAmount'].forEach(id=>{
    const el = document.getElementById(id);
    if (el) {
      el.addEventListener('input', updateTotalsAndStatuses);
    }
  });

  @if($canEdit)
  document.getElementById('resetAll').addEventListener('click', () => {
    if (confirm('Reset all changes?')) {
      location.reload();
    }
  });

  document.getElementById('saveAll').addEventListener('click', (e) => {
    e.preventDefault();
    const projectId = document.getElementById('project_id').value;
    if (!projectId) {
      alert('Please select a project first.');
      return;
    }
    document.getElementById('financeForm').submit();
  });
  @endif
});
</script>
</body>
</html>
