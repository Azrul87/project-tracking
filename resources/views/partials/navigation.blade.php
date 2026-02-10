<!-- Header Section -->
<header class="bg-white shadow-sm">
<div class="flex items-center justify-between px-6 py-3 border-b">
<div class="flex items-center space-x-4">
		<img src="{{ asset('images/logo ecn.png') }}" alt="Logo" class="h-8 w-auto">
<div class="relative">
<button class="flex items-center space-x-2 text-gray-700 font-medium px-3 py-2 rounded-button hover:bg-gray-100 transition-colors">
<span>EC Newenergie</span>
<div class="w-5 h-5 flex items-center justify-center">
<i class="ri-arrow-down-s-line"></i>
</div>
</button>
</div>
</div>
<div class="flex items-center space-x-3">
<button class="p-2 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-5 h-5 flex items-center justify-center">
<a href="{{ route('profile.edit') }}"><i class="ri-settings-4-line"></i></a>
</div>
</button>
<div class="relative ml-2">
    <button class="flex items-center space-x-2 px-2 py-1 rounded-button hover:bg-gray-100 transition-colors focus:outline-none" onclick="toggleProfileMenu(event)">
        <div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden">
            <img src="https://ui-avatars.com/api/?background=6366f1&color=fff&name={{ urlencode(auth()->user()->name ?? 'User') }}" alt="Profile" class="w-full h-full object-cover object-top">
        </div>
        <div class="text-sm text-left">
            <div class="font-medium text-gray-700">{{ auth()->user()->name ?? 'User' }}</div>
            <div class="text-xs text-gray-500">{{ auth()->user()->role ?? 'Member' }}</div>
        </div>
        <div class="w-5 h-5 flex items-center justify-center">
            <i class="ri-arrow-down-s-line text-gray-500"></i>
        </div>
    </button>
    <div id="profileMenu" class="hidden absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-md shadow-lg z-50">
        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
            <i class="ri-user-line mr-2"></i> Manage Profile
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50">
                <i class="ri-logout-box-line mr-2"></i> Logout
            </button>
        </form>
    </div>
</div>
</div>
</div>
<div class="flex items-center justify-between px-6 py-3">
<div class="flex items-center space-x-1">

<a href="/dashboard" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('dashboard') || request()->is('dashboard') ? 'text-primary bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-button transition-colors">Dashboard</a>
<a href="{{ route('clients.index') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('clients.*') || request()->is('clients*') ? 'text-primary bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-button transition-colors">Clients</a>
<a href="/projects" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('projects.*') || request()->is('projects*') ? 'text-primary bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-button transition-colors">Projects</a>
<a href="/finance-tracker" class="px-4 py-2 text-sm font-medium {{ request()->is('finance-tracker') ? 'text-primary bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-button transition-colors">Finance Tracker</a>
<a href="/finance-overview" class="px-4 py-2 text-sm font-medium {{ request()->is('finance-overview') ? 'text-primary bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-button transition-colors">Finance Overview</a>
<a href="/insurance-tracker" class="px-4 py-2 text-sm font-medium {{ request()->is('insurance-tracker') ? 'text-primary bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-button transition-colors">Insurance Tracker</a>
<a href="{{ route('inventory') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('inventory') || request()->is('inventory*') ? 'text-primary bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-button transition-colors">Materials</a>
@if(in_array(auth()->user()->role ?? '', ['Project Manager', 'Supply Chain', 'Finance']))
<a href="{{ route('data-import.index') }}" class="px-4 py-2 text-sm font-medium {{ request()->routeIs('data-import.*') || request()->is('data-import*') ? 'text-primary bg-indigo-50' : 'text-gray-700 hover:bg-gray-100' }} rounded-button transition-colors">Data Import</a>
@endif

</div>

</div>
</header>

<script>
function toggleProfileMenu(event) {
    event.stopPropagation();
    const menu = document.getElementById('profileMenu');
    menu.classList.toggle('hidden');
}
document.addEventListener('click', () => {
    const menu = document.getElementById('profileMenu');
    if (menu && !menu.classList.contains('hidden')) {
        menu.classList.add('hidden');
    }
});
</script>

