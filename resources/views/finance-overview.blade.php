<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Finance Overview</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}};</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<style>
body { font-family: 'Inter', sans-serif; }
.status-badge { 
  display: inline-flex; 
  align-items: center; 
  gap: 4px;
  padding: 6px 14px; 
  border-radius: 9999px; 
  font-size: 12px; 
  font-weight: 600; 
  white-space: nowrap;
}
.status-paid { background-color: #dcfce7; color: #166534; }
.status-pending { background-color: #fef3c7; color: #92400e; }
.status-overdue { background-color: #fecaca; color: #991b1b; }
.custom-input { 
  border: 1px solid #e5e7eb; 
  transition: all 0.2s;
}
.custom-input:focus { 
  outline: none; 
  box-shadow: 0 0 0 3px rgba(79,70,229,0.1); 
  border-color: #4f46e5; 
}
.summary-card {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  padding: 24px;
  color: white;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s, box-shadow 0.2s;
}
.summary-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
}
.summary-card-icon {
  width: 48px;
  height: 48px;
  background: rgba(255,255,255,0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
}
table tbody tr {
  transition: background-color 0.15s;
}
table tbody tr:hover {
  background-color: #f9fafb;
}
.table-header {
  background-color: #f8fafc;
  border-bottom: 2px solid #e2e8f0;
}
</style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
@include('partials.navigation')

<!-- Header with Search and Filters -->
<div class="bg-white border-b shadow-sm">
<div class="container mx-auto px-6 py-4">
<div class="flex items-center justify-between">
<div>
<h1 class="text-2xl font-bold text-gray-800">Finance Overview</h1>
<p class="text-sm text-gray-500 mt-1">Track project finances and payment status</p>
</div>
<div class="flex items-center gap-3">
<div class="relative">
<i class="ri-search-line absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
<input id="search" type="text" class="w-64 rounded-button pl-10 pr-4 py-2.5 custom-input text-sm" placeholder="Search projects or clients...">
</div>
<select id="statusFilter" class="rounded-button px-4 py-2.5 custom-input text-sm font-medium">
<option value="">All Status</option>
<option value="paid">✓ Paid</option>
<option value="pending">⏱ Pending</option>
<option value="overdue">⚠ Overdue</option>
</select>
</div>
</div>
</div>
</div>

<main class="container mx-auto px-6 py-8">
<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
<div class="summary-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
<div class="flex items-start justify-between">
<div>
<p class="text-white/80 text-sm font-medium mb-1">Total Contract Value</p>
<p id="totalContract" class="text-3xl font-bold">RM0</p>
</div>
<div class="summary-card-icon">
<i class="ri-file-list-3-line"></i>
</div>
</div>
</div>

<div class="summary-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
<div class="flex items-start justify-between">
<div>
<p class="text-white/80 text-sm font-medium mb-1">Total Invoiced</p>
<p id="totalInvoiced" class="text-3xl font-bold">RM0</p>
</div>
<div class="summary-card-icon">
<i class="ri-bill-line"></i>
</div>
</div>
</div>

<div class="summary-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
<div class="flex items-start justify-between">
<div>
<p class="text-white/80 text-sm font-medium mb-1">Total Paid</p>
<p id="totalPaid" class="text-3xl font-bold">RM0</p>
</div>
<div class="summary-card-icon">
<i class="ri-money-dollar-circle-line"></i>
</div>
</div>
</div>

<div class="summary-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
<div class="flex items-start justify-between">
<div>
<p class="text-white/80 text-sm font-medium mb-1">Outstanding</p>
<p id="totalOutstanding" class="text-3xl font-bold">RM0</p>
</div>
<div class="summary-card-icon">
<i class="ri-alert-line"></i>
</div>
</div>
</div>
</div>

<!-- Projects Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
<div class="p-6 border-b border-gray-200">
<div class="flex items-center justify-between">
<div>
<h2 class="text-lg font-semibold text-gray-800">Project Finance Details</h2>
<p class="text-sm text-gray-500 mt-1">Contract Total = Project Value + VO</p>
</div>
<div id="projectCount" class="text-sm font-medium text-gray-600"></div>
</div>
</div>

<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead class="table-header">
<tr class="text-left text-gray-700 font-semibold">
<th class="py-4 px-6">Project No</th>
<th class="py-4 px-4">Client</th>
<th class="py-4 px-4">Sales PIC</th>
<th class="py-4 px-4 text-right">Project Value</th>
<th class="py-4 px-4 text-right">VO</th>
<th class="py-4 px-4 text-right">Contract Total</th>
<th class="py-4 px-4 text-right">Total Invoiced</th>
<th class="py-4 px-4 text-right">Total Paid</th>
<th class="py-4 px-4 text-right">Outstanding</th>
<th class="py-4 px-4">Invoice Status</th>
<th class="py-4 px-6">Payment Status</th>
</tr>
</thead>
<tbody id="financeRows" class="divide-y divide-gray-100"></tbody>
</table>
</div>
</div>
</main>

<script>
const fmt = (n)=> 'RM' + (isNaN(n)?0:n).toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});

// Data from database
const projectsData = @json($projectsData);

function compute(project) {
  const totalInvoiced = (project.invoices||[]).reduce((s,x)=>s+(x.amount||0),0) + (project.voInvoiceAmount||0);
  const totalPaid = (project.payments||[]).reduce((s,x)=>s+(x.amount||0),0) + (project.voPaymentAmount||0);
  const contract = (project.projectValue||0) + (project.vo||0);
  const outstanding = Math.max(contract - totalPaid, 0);

  const invCount = (project.invoices||[]).length;
  const invStatus = invCount===0? 'No invoice' : (invCount===1? '1st invoice':''+invCount+' invoices');
  const payCount = (project.payments||[]).length;
  const payStatus = payCount===0? 'pending 1st payment' : (payCount===1? '1st payment received' : ''+payCount+' payments received');

  // Determine overdue if any invoice date has passed and no corresponding payment bringing total >= that invoice cumulative
  let paymentStatusClass = 'status-pending';
  if (totalPaid >= contract) {
    paymentStatusClass = 'status-paid';
  } else {
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const invoices = (project.invoices||[]).sort((a,b) => new Date(a.date||0) - new Date(b.date||0));
    let cumulativeInvoice = 0;
    let cumulativePaid = totalPaid;
    
    for (let inv of invoices) {
      cumulativeInvoice += inv.amount || 0;
      if (inv.date) {
        const invDate = new Date(inv.date);
        invDate.setHours(0, 0, 0, 0);
        if (invDate < today && cumulativePaid < cumulativeInvoice) {
          paymentStatusClass = 'status-overdue';
          break;
        }
      }
    }
  }

  return { totalInvoiced, totalPaid, contract, outstanding, invStatus, payStatus, paymentStatusClass };
}

function updateSummary(list) {
  let totalContract = 0;
  let totalInvoiced = 0;
  let totalPaid = 0;
  let totalOutstanding = 0;
  
  list.forEach(p => {
    const c = compute(p);
    totalContract += c.contract;
    totalInvoiced += c.totalInvoiced;
    totalPaid += c.totalPaid;
    totalOutstanding += c.outstanding;
  });
  
  document.getElementById('totalContract').textContent = fmt(totalContract);
  document.getElementById('totalInvoiced').textContent = fmt(totalInvoiced);
  document.getElementById('totalPaid').textContent = fmt(totalPaid);
  document.getElementById('totalOutstanding').textContent = fmt(totalOutstanding);
  document.getElementById('projectCount').textContent = `${list.length} project${list.length !== 1 ? 's' : ''}`;
}

function render(list){
  const body = document.getElementById('financeRows');
  body.innerHTML = '';
  
  updateSummary(list);
  
  if (list.length === 0) {
    body.innerHTML = `
      <tr>
        <td colspan="11" class="py-12 text-center">
          <i class="ri-inbox-line text-5xl text-gray-300 mb-3"></i>
          <p class="text-gray-500">No projects found</p>
        </td>
      </tr>`;
    return;
  }
  
  list.forEach(p=>{
    const c = compute(p);
    const tr = document.createElement('tr');
    
    // Determine payment status icon
    let payIcon = '<i class="ri-time-line"></i>';
    if (c.paymentStatusClass === 'status-paid') payIcon = '<i class="ri-checkbox-circle-line"></i>';
    if (c.paymentStatusClass === 'status-overdue') payIcon = '<i class="ri-error-warning-line"></i>';
    
    tr.innerHTML = `
      <td class="py-4 px-6">
        <span class="font-semibold text-gray-800">${p.projectNo}</span>
      </td>
      <td class="py-4 px-4 text-gray-700">${p.client}</td>
      <td class="py-4 px-4 text-gray-600">${p.salesPic||'-'}</td>
      <td class="py-4 px-4 text-right font-medium text-gray-800">${fmt(p.projectValue||0)}</td>
      <td class="py-4 px-4 text-right font-medium text-gray-800">${fmt(p.vo||0)}</td>
      <td class="py-4 px-4 text-right font-semibold text-gray-900">${fmt(c.contract)}</td>
      <td class="py-4 px-4 text-right font-medium text-blue-600">${fmt(c.totalInvoiced)}</td>
      <td class="py-4 px-4 text-right font-medium text-green-600">${fmt(c.totalPaid)}</td>
      <td class="py-4 px-4 text-right font-medium ${c.outstanding > 0 ? 'text-orange-600' : 'text-gray-400'}">${fmt(c.outstanding)}</td>
      <td class="py-4 px-4">
        <span class="status-badge status-pending">
          <i class="ri-bill-line"></i>${c.invStatus}
        </span>
      </td>
      <td class="py-4 px-6">
        <span class="status-badge ${c.paymentStatusClass}">
          ${payIcon}${c.payStatus}
        </span>
      </td>
    `;
    body.appendChild(tr);
  });
}

function applyFilters(){
  const q = document.getElementById('search').value.toLowerCase();
  const status = document.getElementById('statusFilter').value;
  let list = projectsData.filter(p => 
    p.projectNo.toLowerCase().includes(q) || 
    p.client.toLowerCase().includes(q) ||
    (p.salesPic||'').toLowerCase().includes(q)
  );
  if (status) {
    list = list.filter(p => {
      const c = compute(p);
      if (status==='paid') return c.totalPaid >= c.contract;
      if (status==='overdue') return c.paymentStatusClass==='status-overdue';
      if (status==='pending') return c.totalPaid < c.contract && c.paymentStatusClass!=='status-overdue';
      return true;
    });
  }
  render(list);
}

document.addEventListener('DOMContentLoaded', ()=>{
  render(projectsData);
  document.getElementById('search').addEventListener('input', applyFilters);
  document.getElementById('statusFilter').addEventListener('change', applyFilters);
  
  // Set search value from URL if present
  const urlParams = new URLSearchParams(window.location.search);
  const searchParam = urlParams.get('search');
  if (searchParam) {
    document.getElementById('search').value = searchParam;
    applyFilters();
  }
});
</script>
</body>
</html>


