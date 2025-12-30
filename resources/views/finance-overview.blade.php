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
.status-badge { padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
.status-paid { background-color: #dcfce7; color: #166534; }
.status-pending { background-color: #fef3c7; color: #92400e; }
.status-overdue { background-color: #fecaca; color: #991b1b; }
.custom-input { border: 1px solid #e5e7eb; }
.custom-input:focus { outline: none; box-shadow: 0 0 0 3px rgba(79,70,229,0.15); border-color: #4f46e5; }
</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')
<div class="bg-white border-b px-6 py-3">
<div class="flex items-center justify-end space-x-3">
<input id="search" type="text" class="w-56 rounded-button p-2 custom-input" placeholder="Search by project or client">
<select id="statusFilter" class="rounded-button p-2 custom-input">
<option value="">All status</option>
<option value="paid">Paid</option>
<option value="pending">Pending</option>
<option value="overdue">Overdue</option>
</select>
</div>
</div>

<main class="container mx-auto px-6 py-6">
<div class="bg-white rounded shadow-sm p-6">
<div class="flex items-center justify-between mb-4">
<h1 class="text-xl font-semibold text-gray-800">Finance Overview</h1>
<div class="text-sm text-gray-500">Contract = Project Value + VO</div>
</div>

<div class="overflow-x-auto">
<table class="min-w-full text-sm">
<thead>
<tr class="text-left text-gray-600">
<th class="py-2 pr-4">Project No</th>
<th class="py-2 pr-4">Client</th>
<th class="py-2 pr-4">Sales PIC</th>
<th class="py-2 pr-4 text-right">Project Value</th>
<th class="py-2 pr-4 text-right">VO</th>
<th class="py-2 pr-4 text-right">Contract Total</th>
<th class="py-2 pr-4 text-right">Total Invoiced</th>
<th class="py-2 pr-4 text-right">Total Paid</th>
<th class="py-2 pr-4 text-right">Outstanding</th>
<th class="py-2 pr-4">Invoice Status</th>
<th class="py-2 pr-4">Payment Status</th>
</tr>
</thead>
<tbody id="financeRows" class="divide-y divide-gray-100"></tbody>
</table>
</div>
</div>
</main>

<script>
const fmt = (n)=> (isNaN(n)?0:n).toLocaleString(undefined,{style:'currency',currency:'USD'});

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

function render(list){
  const body = document.getElementById('financeRows');
  body.innerHTML = '';
  if (list.length === 0) {
    body.innerHTML = '<tr><td colspan="11" class="py-4 text-center text-gray-500">No projects found</td></tr>';
    return;
  }
  list.forEach(p=>{
    const c = compute(p);
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="py-2 pr-4 text-gray-800">${p.projectNo}</td>
      <td class="py-2 pr-4 text-gray-600">${p.client}</td>
      <td class="py-2 pr-4 text-gray-600">${p.salesPic||''}</td>
      <td class="py-2 pr-4 text-right text-gray-800">${fmt(p.projectValue||0)}</td>
      <td class="py-2 pr-4 text-right text-gray-800">${fmt(p.vo||0)}</td>
      <td class="py-2 pr-4 text-right text-gray-800">${fmt(c.contract)}</td>
      <td class="py-2 pr-4 text-right text-gray-800">${fmt(c.totalInvoiced)}</td>
      <td class="py-2 pr-4 text-right text-gray-800">${fmt(c.totalPaid)}</td>
      <td class="py-2 pr-4 text-right text-gray-800">${fmt(c.outstanding)}</td>
      <td class="py-2 pr-4"><span class="status-badge status-pending">${c.invStatus}</span></td>
      <td class="py-2 pr-4"><span class="status-badge ${c.paymentStatusClass}">${c.payStatus}</span></td>
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


