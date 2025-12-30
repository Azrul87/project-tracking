<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Project Overview</title>
<script src="https://cdn.tailwindcss.com/3.4.16"></script>
<script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.5.0/echarts.min.js"></script>
<style>
:where([class^="ri-"])::before { content: "\f3c2"; }
body {
font-family: 'Inter', sans-serif;
}
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
-webkit-appearance: none;
margin: 0;
}
input[type="number"] {
-moz-appearance: textfield;
}
.custom-scrollbar::-webkit-scrollbar {
width: 6px;
height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
background: #f1f1f1;
border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
background: #d1d5db;
border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
background: #9ca3af;
}
.custom-switch {
position: relative;
display: inline-block;
width: 36px;
height: 20px;
}
.custom-switch input {
opacity: 0;
width: 0;
height: 0;
}
.switch-slider {
position: absolute;
cursor: pointer;
top: 0;
left: 0;
right: 0;
bottom: 0;
background-color: #e5e7eb;
transition: .4s;
border-radius: 20px;
}
.switch-slider:before {
position: absolute;
content: "";
height: 16px;
width: 16px;
left: 2px;
bottom: 2px;
background-color: white;
transition: .4s;
border-radius: 50%;
}
input:checked + .switch-slider {
background-color: #4f46e5;
}
input:checked + .switch-slider:before {
transform: translateX(16px);
}
.custom-checkbox {
appearance: none;
width: 18px;
height: 18px;
border: 2px solid #d1d5db;
border-radius: 4px;
cursor: pointer;
position: relative;
}
.custom-checkbox:checked {
background-color: #4f46e5;
border-color: #4f46e5;
}
.custom-checkbox:checked::after {
content: "";
position: absolute;
left: 5px;
top: 2px;
width: 6px;
height: 10px;
border: solid white;
border-width: 0 2px 2px 0;
transform: rotate(45deg);
}
.stat-card {
transition: transform 0.2s ease-in-out;
}
.stat-card:hover {
transform: translateY(-2px);
}
</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')
<!-- <button class="px-4 py-2 text-sm font-medium text-gray-700 rounded-button hover:bg-gray-100 transition-colors">Files</button>
<button class="px-4 py-2 text-sm font-medium text-gray-700 rounded-button hover:bg-gray-100 transition-colors">Reports</button> -->
</div>
<div class="flex items-center space-x-3">
<button class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 rounded-button hover:bg-gray-100 transition-colors whitespace-nowrap">
<div class="w-4 h-4 flex items-center justify-center">
<!-- <i class="ri-filter-3-line"></i> -->
<!-- </div>
<span>Filter</span>
</button> -->
<button class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600 transition-colors whitespace-nowrap">
<a href="{{ route('projects.create') }}" class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600 transition-colors whitespace-nowrap">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-add-line"></i>
</div>
<span>New Project</span>
</a>
</div>
</div>
</header>

<!-- Main Content -->
<main class="container mx-auto px-6 py-6">
<!-- Welcome Section -->
<div class="mb-8">
<div class="bg-gradient-to-r from-primary to-secondary rounded-lg p-8 text-white">
<div class="flex items-center justify-between">
<div>
<h1 class="text-3xl font-bold mb-2">Welcome back, {{ auth()->user()->name ?? 'User' }}!</h1>
<p class="text-indigo-100 text-lg">Here's what's happening with your projects today.</p>
</div>
<div class="hidden md:block">
<div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
<i class="ri-dashboard-3-line text-4xl"></i>
</div>
</div>
</div>
</div>
</div>

<!-- Key Metrics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
<div class="stat-card bg-white rounded-lg shadow-sm p-6">
<div class="flex items-center justify-between">
<div>
<div class="text-sm font-medium text-gray-500 mb-1">Total Projects</div>
<div class="text-2xl font-bold text-gray-900">12</div>
<div class="text-xs text-green-600 flex items-center mt-1">
<i class="ri-arrow-up-line mr-1"></i>
<span>+2 this month</span>
</div>
</div>
<div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
<i class="ri-folder-line text-blue-600 text-xl"></i>
</div>
</div>
</div>

<div class="stat-card bg-white rounded-lg shadow-sm p-6">
<div class="flex items-center justify-between">
<div>
<div class="text-sm font-medium text-gray-500 mb-1">Active Tasks</div>
<div class="text-2xl font-bold text-gray-900">78</div>
<div class="text-xs text-yellow-600 flex items-center mt-1">
<i class="ri-time-line mr-1"></i>
<span>15 due today</span>
</div>
</div>
<div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
<i class="ri-task-line text-green-600 text-xl"></i>
</div>
</div>
</div>

<div class="stat-card bg-white rounded-lg shadow-sm p-6">
<div class="flex items-center justify-between">
<div>
<div class="text-sm font-medium text-gray-500 mb-1">Team Members</div>
<div class="text-2xl font-bold text-gray-900">24</div>
<div class="text-xs text-blue-600 flex items-center mt-1">
<i class="ri-user-line mr-1"></i>
<span>18 online now</span>
</div>
</div>
<div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
<i class="ri-team-line text-purple-600 text-xl"></i>
</div>
</div>
</div>

<div class="stat-card bg-white rounded-lg shadow-sm p-6">
<div class="flex items-center justify-between">
<div>
<div class="text-sm font-medium text-gray-500 mb-1">Budget Used</div>
<div class="text-2xl font-bold text-gray-900">$124K</div>
<div class="text-xs text-red-600 flex items-center mt-1">
<i class="ri-money-dollar-circle-line mr-1"></i>
<span>78% of budget</span>
</div>
</div>
<div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
<i class="ri-money-dollar-circle-line text-yellow-600 text-xl"></i>
</div>
</div>
</div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-12 gap-6">
<!-- Projects Overview -->
<div class="col-span-12 lg:col-span-8 bg-white rounded-lg shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-xl font-semibold text-gray-800">Recent Projects</h2>
<button class="text-sm text-primary hover:text-indigo-700 whitespace-nowrap">View all</button>
</div>
<div class="space-y-4">
<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
<div class="flex items-center justify-between mb-3">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
<i class="ri-solar-energy-line text-blue-600"></i>
</div>
<div>
<h3 class="font-semibold text-gray-800">Solar Panel Installation</h3>
<p class="text-sm text-gray-500">EC Newenergie - Residential</p>
</div>
</div>
<div class="text-right">
<div class="text-sm font-medium text-gray-800">85%</div>
<div class="text-xs text-green-600">On Track</div>
</div>
</div>
<div class="w-full bg-gray-200 rounded-full h-2">
<div class="bg-green-500 h-2 rounded-full" style="width: 85%"></div>
</div>
<div class="flex items-center justify-between mt-3 text-sm text-gray-500">
<span>Due: Jun 30, 2025</span>
<span>Team: 8 members</span>
</div>
</div>

<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
<div class="flex items-center justify-between mb-3">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
<i class="ri-windy-line text-green-600"></i>
</div>
<div>
<h3 class="font-semibold text-gray-800">Wind Farm Development</h3>
<p class="text-sm text-gray-500">EC Newenergie - Commercial</p>
</div>
</div>
<div class="text-right">
<div class="text-sm font-medium text-gray-800">62%</div>
<div class="text-xs text-yellow-600">At Risk</div>
</div>
</div>
<div class="w-full bg-gray-200 rounded-full h-2">
<div class="bg-yellow-500 h-2 rounded-full" style="width: 62%"></div>
</div>
<div class="flex items-center justify-between mt-3 text-sm text-gray-500">
<span>Due: Aug 15, 2025</span>
<span>Team: 12 members</span>
</div>
</div>

<div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
<div class="flex items-center justify-between mb-3">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
<i class="ri-battery-line text-purple-600"></i>
</div>
<div>
<h3 class="font-semibold text-gray-800">Energy Storage System</h3>
<p class="text-sm text-gray-500">EC Newenergie - Industrial</p>
</div>
</div>
<div class="text-right">
<div class="text-sm font-medium text-gray-800">34%</div>
<div class="text-xs text-blue-600">In Progress</div>
</div>
</div>
<div class="w-full bg-gray-200 rounded-full h-2">
<div class="bg-blue-500 h-2 rounded-full" style="width: 34%"></div>
</div>
<div class="flex items-center justify-between mt-3 text-sm text-gray-500">
<span>Due: Sep 20, 2025</span>
<span>Team: 6 members</span>
</div>
</div>
</div>
</div>

<!-- Quick Stats & Charts -->
<div class="col-span-12 lg:col-span-4 bg-white rounded-lg shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Project Health</h2>
<button class="text-sm text-primary hover:text-indigo-700 whitespace-nowrap">Details</button>
</div>
<div class="space-y-4">
<div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
<div class="flex items-center space-x-3">
<div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
<i class="ri-check-line text-green-600 text-sm"></i>
</div>
<div>
<div class="text-sm font-medium text-gray-800">On Track</div>
<div class="text-xs text-gray-500">8 projects</div>
</div>
</div>
<div class="text-lg font-bold text-green-600">67%</div>
</div>

<div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
<div class="flex items-center space-x-3">
<div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
<i class="ri-alert-line text-yellow-600 text-sm"></i>
</div>
<div>
<div class="text-sm font-medium text-gray-800">At Risk</div>
<div class="text-xs text-gray-500">3 projects</div>
</div>
</div>
<div class="text-lg font-bold text-yellow-600">25%</div>
</div>

<div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
<div class="flex items-center space-x-3">
<div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
<i class="ri-error-warning-line text-red-600 text-sm"></i>
</div>
<div>
<div class="text-sm font-medium text-gray-800">Delayed</div>
<div class="text-xs text-gray-500">1 project</div>
</div>
</div>
<div class="text-lg font-bold text-red-600">8%</div>
</div>
</div>

<div class="mt-6">
<div class="flex justify-between items-center mb-4">
<h3 class="text-sm font-medium text-gray-700">Project Distribution</h3>
</div>
<div id="projectDistributionChart" class="h-48"></div>
</div>
</div>

<!-- Upcoming Deadlines -->
<div class="col-span-12 lg:col-span-6 bg-white rounded-lg shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Upcoming Deadlines</h2>
<button class="text-sm text-primary hover:text-indigo-700 whitespace-nowrap">View calendar</button>
</div>
<div class="space-y-4">
<div class="flex items-center space-x-4 p-3 border border-gray-200 rounded-lg">
<div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
<div class="text-center">
<div class="text-sm font-bold text-red-600">15</div>
<div class="text-xs text-red-500">MAY</div>
</div>
</div>
<div class="flex-1">
<h3 class="font-medium text-gray-800">Design Assets Delivery</h3>
<p class="text-sm text-gray-500">Solar Panel Installation</p>
</div>
<div class="text-right">
<div class="text-xs text-red-600 font-medium">Due Today</div>
<div class="text-xs text-gray-500">High Priority</div>
</div>
</div>

<div class="flex items-center space-x-4 p-3 border border-gray-200 rounded-lg">
<div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
<div class="text-center">
<div class="text-sm font-bold text-yellow-600">18</div>
<div class="text-xs text-yellow-500">MAY</div>
</div>
</div>
<div class="flex-1">
<h3 class="font-medium text-gray-800">Client Presentation</h3>
<p class="text-sm text-gray-500">Wind Farm Development</p>
</div>
<div class="text-right">
<div class="text-xs text-yellow-600 font-medium">3 days</div>
<div class="text-xs text-gray-500">Medium Priority</div>
</div>
</div>

<div class="flex items-center space-x-4 p-3 border border-gray-200 rounded-lg">
<div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
<div class="text-center">
<div class="text-sm font-bold text-blue-600">22</div>
<div class="text-xs text-blue-500">MAY</div>
</div>
</div>
<div class="flex-1">
<h3 class="font-medium text-gray-800">Budget Review</h3>
<p class="text-sm text-gray-500">Energy Storage System</p>
</div>
<div class="text-right">
<div class="text-xs text-blue-600 font-medium">7 days</div>
<div class="text-xs text-gray-500">Low Priority</div>
</div>
</div>
</div>
</div>

<!-- Team Performance -->
<div class="col-span-12 lg:col-span-6 bg-white rounded-lg shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Team Performance</h2>
<button class="text-sm text-primary hover:text-indigo-700 whitespace-nowrap">View details</button>
</div>
<div class="space-y-4">
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20young%20business%20woman%20with%20short%20brown%20hair%2C%20neutral%20expression%2C%20business%20attire%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=1&orientation=squarish" alt="Emily Parker" class="w-full h-full object-cover object-top">
</div>
<div>
<div class="font-medium text-gray-800">Emily Parker</div>
<div class="text-xs text-gray-500">Project Manager</div>
</div>
</div>
<div class="text-right">
<div class="text-sm font-bold text-green-600">95%</div>
<div class="text-xs text-gray-500">12 tasks</div>
</div>
</div>

<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20an%20asian%20man%20in%20his%2030s%2C%20short%20black%20hair%2C%20wearing%20business%20casual%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=2&orientation=squarish" alt="David Chen" class="w-full h-full object-cover object-top">
</div>
<div>
<div class="font-medium text-gray-800">David Chen</div>
<div class="text-xs text-gray-500">Lead Designer</div>
</div>
</div>
<div class="text-right">
<div class="text-sm font-bold text-yellow-600">78%</div>
<div class="text-xs text-gray-500">8 tasks</div>
</div>
</div>

<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20black%20woman%20in%20her%2020s%20with%20natural%20hair%2C%20wearing%20business%20casual%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=3&orientation=squarish" alt="Sophia Williams" class="w-full h-full object-cover object-top">
</div>
<div>
<div class="font-medium text-gray-800">Sophia Williams</div>
<div class="text-xs text-gray-500">Content Strategist</div>
</div>
</div>
<div class="text-right">
<div class="text-sm font-bold text-green-600">88%</div>
<div class="text-xs text-gray-500">15 tasks</div>
</div>
</div>

<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20caucasian%20man%20in%20his%2040s%20with%20glasses%20and%20short%20brown%20hair%2C%20wearing%20business%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=4&orientation=squarish" alt="Michael Johnson" class="w-full h-full object-cover object-top">
</div>
<div>
<div class="font-medium text-gray-800">Michael Johnson</div>
<div class="text-xs text-gray-500">SEO Specialist</div>
</div>
</div>
<div class="text-right">
<div class="text-sm font-bold text-green-600">92%</div>
<div class="text-xs text-gray-500">10 tasks</div>
</div>
</div>
</div>
</div>

<!-- Recent Activity -->
<div class="col-span-12 bg-white rounded-lg shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Recent Activity</h2>
<div class="flex items-center space-x-2">
<button class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors whitespace-nowrap">All</button>
<button class="px-3 py-1 text-xs font-medium text-gray-500 rounded-full hover:bg-gray-100 transition-colors whitespace-nowrap">Projects</button>
<button class="px-3 py-1 text-xs font-medium text-gray-500 rounded-full hover:bg-gray-100 transition-colors whitespace-nowrap">Tasks</button>
<button class="px-3 py-1 text-xs font-medium text-gray-500 rounded-full hover:bg-gray-100 transition-colors whitespace-nowrap">Team</button>
</div>
</div>
<div class="space-y-4">
<div class="flex space-x-3">
<div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20young%20business%20woman%20with%20short%20brown%20hair%2C%20neutral%20expression%2C%20business%20attire%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=1&orientation=squarish" alt="Emily Parker" class="w-full h-full object-cover object-top">
</div>
<div class="flex-1">
<div class="flex justify-between">
<div>
<span class="font-medium text-gray-800">Emily Parker</span>
<span class="text-gray-600 text-sm"> created a new project </span>
<span class="font-medium text-blue-600">Energy Storage System</span>
</div>
<div class="text-xs text-gray-500">2 hours ago</div>
</div>
<div class="mt-1 text-sm text-gray-600">
"New industrial energy storage project for EC Newenergie. Initial planning phase starting next week."
</div>
</div>
</div>

<div class="flex space-x-3">
<div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20an%20asian%20man%20in%20his%2030s%2C%20short%20black%20hair%2C%20wearing%20business%20casual%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=2&orientation=squarish" alt="David Chen" class="w-full h-full object-cover object-top">
</div>
<div class="flex-1">
<div class="flex justify-between">
<div>
<span class="font-medium text-gray-800">David Chen</span>
<span class="text-gray-600 text-sm"> completed </span>
<span class="font-medium text-gray-800">Design Mockups</span>
</div>
<div class="text-xs text-gray-500">4 hours ago</div>
</div>
<div class="mt-1 text-sm text-gray-600">
"Final design mockups for the solar panel installation project are ready for client review."
</div>
</div>
</div>

<div class="flex space-x-3">
<div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20black%20woman%20in%20her%2020s%20with%20natural%20hair%2C%20wearing%20business%20casual%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=3&orientation=squarish" alt="Sophia Williams" class="w-full h-full object-cover object-top">
</div>
<div class="flex-1">
<div class="flex justify-between">
<div>
<span class="font-medium text-gray-800">Sophia Williams</span>
<span class="text-gray-600 text-sm"> updated project status to </span>
<span class="font-medium text-green-600">On Track</span>
</div>
<div class="text-xs text-gray-500">6 hours ago</div>
</div>
<div class="mt-1 text-sm text-gray-600">
"Wind farm development project is progressing well. All content deliverables are on schedule."
</div>
</div>
</div>
</div>
</div>
</div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
// Project Distribution Chart
const projectDistributionChart = echarts.init(document.getElementById('projectDistributionChart'));
const projectDistributionOption = {
animation: false,
tooltip: {
trigger: 'item',
backgroundColor: 'rgba(255, 255, 255, 0.9)',
borderColor: '#e5e7eb',
borderWidth: 1,
textStyle: {
color: '#1f2937'
}
},
legend: {
bottom: '0%',
left: 'center',
itemWidth: 12,
itemHeight: 12,
textStyle: {
color: '#1f2937'
}
},
series: [
{
name: 'Project Status',
type: 'pie',
radius: ['40%', '70%'],
center: ['50%', '45%'],
avoidLabelOverlap: false,
itemStyle: {
borderRadius: 6,
borderColor: '#fff',
borderWidth: 2
},
label: {
show: false
},
emphasis: {
label: {
show: false
}
},
labelLine: {
show: false
},
data: [
{ value: 8, name: 'On Track', itemStyle: { color: 'rgba(34, 197, 94, 1)' } },
{ value: 3, name: 'At Risk', itemStyle: { color: 'rgba(251, 191, 36, 1)' } },
{ value: 1, name: 'Delayed', itemStyle: { color: 'rgba(239, 68, 68, 1)' } }
]
}
]
};
projectDistributionChart.setOption(projectDistributionOption);

// Resize charts when window size changes
window.addEventListener('resize', function() {
projectDistributionChart.resize();
});
});
</script>
</body>
</html>
