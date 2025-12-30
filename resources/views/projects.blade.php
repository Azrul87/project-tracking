<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projects - Project Tracking System</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
            min-height: 100vh;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
            border-radius: 10px;
            border: 2px solid #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
        }
        .status-badge {
            padding: 6px 14px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            display: inline-flex;
            align-items: center;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        .status-badge:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .status-planning { 
            background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
            color: #3730a3;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }
        .status-active { 
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        .status-pending { 
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .status-completed { 
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .status-on-hold { 
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .status-in-progress { 
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            color: #1e40af;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .status-not-started { 
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #374151;
            border: 1px solid rgba(107, 114, 128, 0.2);
        }
        .status-badge:not([class*="status-"]) { 
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            color: #374151;
            border: 1px solid rgba(107, 114, 128, 0.2);
        }
        .payment-paid { 
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
            color: #166534;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        .payment-pending { 
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            color: #92400e;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        .payment-overdue { 
            background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
            color: #991b1b;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .table-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }
        .table-container table {
            width: 100%;
            min-width: 1400px;
        }
        @media (min-width: 1536px) {
            .table-container table {
                min-width: 100%;
            }
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.06);
        }
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.3);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(79, 70, 229, 0.4);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            border: 1px solid rgba(148, 163, 184, 0.3);
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
        }
        .input-field {
            transition: all 0.3s ease;
            border: 1.5px solid #e2e8f0;
        }
        .input-field:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        .table-row {
            transition: all 0.2s ease;
        }
        .table-row:hover {
            background: linear-gradient(90deg, #f8fafc 0%, #f1f5f9 100%);
            transform: scale(1.001);
        }
        .action-btn {
            transition: all 0.2s ease;
            padding: 6px;
            border-radius: 8px;
        }
        .action-btn:hover {
            transform: translateY(-2px) scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .action-btn.view:hover {
            background: rgba(59, 130, 246, 0.1);
        }
        .action-btn.edit:hover {
            background: rgba(99, 102, 241, 0.1);
        }
        .action-btn.delete:hover {
            background: rgba(239, 68, 68, 0.1);
        }
        .header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
@include('partials.navigation')

    <!-- Main Content -->
    <div class="w-full px-2 sm:px-4 lg:px-8 py-8">
        <!-- Header -->
        <div class="px-4 py-6 sm:px-0">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold header-gradient mb-2">All Projects</h1>
                    <p class="text-gray-600 font-medium">Manage and track all your projects efficiently</p>
                </div>
                <a href="{{ route('projects.create') }}" class="btn-primary text-white px-6 py-3 rounded-xl font-semibold flex items-center space-x-2 shadow-lg">
                    <i class="ri-add-line text-xl"></i>
                    <span>Add Project</span>
                </a>
            </div>

            @if(session('success'))
            <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="ri-checkbox-circle-line text-xl mr-2"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
            @endif

            <!-- Filter Bar -->
            <form method="GET" action="{{ route('projects.index') }}" class="glass-card rounded-2xl p-6 mb-6 shadow-xl">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5">
                    <!-- Search Box -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-search-line mr-1"></i>Search
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="ri-search-line text-gray-400 text-lg"></i>
                            </div>
                            <input type="text" 
                                   name="search"
                                   id="search"
                                   value="{{ request('search') }}"
                                   placeholder="Search by project no, client, or location..." 
                                   class="input-field block w-full pl-12 pr-4 py-3 rounded-xl focus:outline-none bg-white">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-bar-chart-line mr-1"></i>Status
                        </label>
                        <select name="status" id="status" class="input-field block w-full px-4 py-3 rounded-xl focus:outline-none bg-white">
                            <option value="">All Status</option>
                            @foreach($statuses ?? [] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-folder-line mr-1"></i>Category
                        </label>
                        <select name="category" id="category" class="input-field block w-full px-4 py-3 rounded-xl focus:outline-none bg-white">
                            <option value="">All Categories</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Installer Filter -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-user-line mr-1"></i>Installer
                        </label>
                        <select name="installer" id="installer" class="input-field block w-full px-4 py-3 rounded-xl focus:outline-none bg-white">
                            <option value="">All Installers</option>
                            @foreach($installers ?? [] as $installer)
                                <option value="{{ $installer }}" {{ request('installer') == $installer ? 'selected' : '' }}>
                                    {{ $installer }}
                                </option>
                            @endforeach
                            @if($hasOtherInstaller ?? false)
                                <option value="other" {{ request('installer') == 'other' ? 'selected' : '' }}>Other</option>
                            @endif
                        </select>
                    </div>
                </div>

                <!-- Payment Status Filter -->
                <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-end gap-4">
                    <div class="flex-1 sm:w-1/4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="ri-money-dollar-circle-line mr-1"></i>Payment Status
                        </label>
                        <select name="payment_status" id="payment_status" class="input-field block w-full px-4 py-3 rounded-xl focus:outline-none bg-white">
                            <option value="">All Payment Status</option>
                            @foreach($paymentStatuses ?? [] as $paymentStatus)
                                <option value="{{ $paymentStatus }}" {{ request('payment_status') == $paymentStatus ? 'selected' : '' }}>
                                    {{ ucfirst($paymentStatus) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" class="btn-primary text-white px-6 py-3 rounded-xl font-semibold flex items-center space-x-2">
                            <i class="ri-search-line"></i>
                            <span>Apply Filters</span>
                        </button>
                        <a href="{{ route('projects.index') }}" class="btn-secondary text-gray-700 px-6 py-3 rounded-xl font-semibold flex items-center space-x-2">
                            <i class="ri-close-line"></i>
                            <span>Clear</span>
                        </a>
                    </div>
                </div>
            </form>

            <!-- Data Table -->
            <div class="glass-card rounded-2xl overflow-hidden w-full shadow-xl">
                <div class="table-container custom-scrollbar w-full">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Project No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Client</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Location</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Payment Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Installer</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Installation Date</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Sales PIC</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap min-w-[120px]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($projects as $project)
                            <tr class="table-row">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-900">{{ $project->project_id }}</span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ $project->client->client_name ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-5 text-sm text-gray-600 max-w-xs truncate" title="{{ $project->location ?? 'N/A' }}">
                                    <div class="flex items-center">
                                        <i class="ri-map-pin-line mr-2 text-gray-400"></i>
                                        {{ $project->location ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @php
                                        $status = strtolower(str_replace(' ', '-', $project->status ?? 'planning'));
                                    @endphp
                                    <span class="status-badge status-{{ $status }}">
                                        {{ $project->status ?? 'Planning' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap">
                                    @php
                                        $paymentStatus = strtolower($project->payment_status ?? 'pending');
                                    @endphp
                                    <span class="status-badge payment-{{ $paymentStatus }}">
                                        {{ $project->payment_status ?? 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-800">
                                    @if($project->installer === 'Other' && $project->installer_other)
                                        <div class="flex items-center">
                                            <i class="ri-user-line mr-2 text-gray-400"></i>
                                            {{ $project->installer_other }}
                                        </div>
                                    @else
                                        <div class="flex items-center">
                                            <i class="ri-user-line mr-2 text-gray-400"></i>
                                            {{ $project->installer ?? '-' }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                                    @if($project->installation_date)
                                        <div class="flex items-center">
                                            <i class="ri-calendar-line mr-2 text-gray-400"></i>
                                            {{ $project->installation_date->format('Y-m-d') }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium text-gray-800">
                                    <div class="flex items-center">
                                        <i class="ri-user-star-line mr-2 text-gray-400"></i>
                                        {{ $project->salesPic->name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 whitespace-nowrap text-sm font-medium min-w-[120px]">
                                    <div class="flex space-x-2 items-center">
                                        <a href="{{ route('projects.dashboard', $project->project_id) }}" class="action-btn view text-blue-600 hover:text-blue-700" title="View Dashboard">
                                            <i class="ri-eye-line text-xl"></i>
                                        </a>
                                        <a href="{{ route('projects.edit', $project->project_id) }}" class="action-btn edit text-indigo-600 hover:text-indigo-700" title="Edit">
                                            <i class="ri-pencil-line text-xl"></i>
                                        </a>
                                        <form action="{{ route('projects.destroy', $project->project_id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this project?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete text-red-600 hover:text-red-700" title="Delete">
                                                <i class="ri-delete-bin-line text-xl"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="bg-gradient-to-br from-gray-100 to-gray-200 rounded-full p-6 mb-4">
                                            <i class="ri-inbox-line text-5xl text-gray-400"></i>
                                        </div>
                                        <p class="text-xl font-bold text-gray-700 mb-2">No projects found</p>
                                        <p class="text-sm text-gray-500 mb-4">
                                            @if(request()->hasAny(['search', 'status', 'category', 'installer', 'payment_status']))
                                                Try adjusting your filters or 
                                                <a href="{{ route('projects.index') }}" class="text-primary hover:underline font-semibold">clear all filters</a>
                                            @else
                                                <a href="{{ route('projects.create') }}" class="text-primary hover:underline font-semibold">Create your first project</a>
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Results Count -->
                @if($projects->count() > 0)
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-700">
                            <i class="ri-file-list-line mr-2"></i>
                            Showing <span class="text-primary font-bold">{{ $projects->count() }}</span> {{ Str::plural('project', $projects->count()) }}
                            @if(request()->hasAny(['search', 'status', 'category', 'installer', 'payment_status']))
                                <span class="text-gray-500 font-normal">(filtered)</span>
                            @endif
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth animations
            const rows = document.querySelectorAll('.table-row');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });

            // Enhanced search with debounce
            const filterSelects = document.querySelectorAll('#status, #category, #installer, #payment_status');
            const searchInput = document.getElementById('search');
            let searchTimeout;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        // Optional: Auto-submit on search after 500ms of no typing
                        // Uncomment the line below if you want auto-search
                        // document.querySelector('form').submit();
                    }, 500);
                });
            }
        });
    </script>
</body>
</html>