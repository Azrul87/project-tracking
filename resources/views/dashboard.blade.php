<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Project Dashboard</title>
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
.gantt-chart .task {
height: 24px;
border-radius: 4px;
margin: 2px 0;
}
.task-on-track {
background-color: rgba(87, 181, 231, 0.8);
}
.task-at-risk {
background-color: rgba(251, 191, 114, 0.8);
}
.task-delayed {
background-color: rgba(252, 141, 98, 0.8);
}
.timeline-dot {
width: 12px;
height: 12px;
border-radius: 50%;
}
.timeline-line {
width: 2px;
background-color: #e5e7eb;
}
.milestone-completed {
background-color: #10b981;
}
.milestone-upcoming {
background-color: #6366f1;
}
.milestone-overdue {
background-color: #ef4444;
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
</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')
<!-- Main Content -->
<main class="container mx-auto px-6 py-6">
@if(session('success'))
<div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-800 rounded">{{ session('success') }}</div>
@endif
<div class="grid grid-cols-12 gap-6">
<!-- Project Overview Card -->
<div class="col-span-12 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-start mb-6">
<div>
<h2 class="text-xl font-semibold text-gray-800">{{ $project['project_no'] ?? 'Project Ongoing' }}</h2>
@if(isset($project))
<div class="text-sm text-gray-600 mt-1">{{ $project['client'] }} • {{ $project['location'] }}</div>
@endif
<div class="flex items-center mt-2 space-x-4">
<div class="flex items-center space-x-1">
<div class="w-4 h-4 flex items-center justify-center text-green-500">
<i class="ri-calendar-line"></i>
</div>
<span class="text-sm text-gray-600">Apr 15 - Jun 30, 2025</span>
</div>
<!-- team size hidden -->
<div class="flex items-center space-x-1">
<div class="w-4 h-4 flex items-center justify-center text-yellow-500">
<i class="ri-flag-line"></i>
</div>
<span class="text-sm text-gray-600">High Priority</span>
</div>
</div>
</div>
<button class="p-2 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-5 h-5 flex items-center justify-center">
<i class="ri-more-2-fill"></i>
</div>
</button>
</div>
<div class="grid grid-cols-3 gap-4 mb-6">
<div class="bg-gray-50 p-4 rounded">
<div class="text-sm text-gray-500 mb-1">Payment Progress</div>
<div class="space-y-2">
<div class="flex items-center justify-between">
<div class="text-sm text-gray-600">Initial Payment</div>
<div class="text-sm font-medium text-green-600">Received</div>
</div>
<div class="flex items-center justify-between">
<div class="text-sm text-gray-600">NEM Approval</div>
<div class="text-sm font-medium text-yellow-600">Pending</div>
</div>
<div class="flex items-center justify-between">
<div class="text-sm text-gray-600">Final Payment</div>
<div class="text-sm font-medium text-gray-400">Upcoming</div>
</div>
<div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
<div class="bg-primary h-1.5 rounded-full" style="width: 33%"></div>
</div>
</div>
</div>
<div class="bg-gray-50 p-4 rounded">
<div class="text-sm text-gray-500 mb-1">Tasks</div>
<div class="flex items-end justify-between">
<div class="text-xl font-semibold text-gray-800">42/78</div>
<div class="text-sm text-gray-600">54% completed</div>
</div>
</div>
<div class="bg-gray-50 p-4 rounded">
<div class="text-sm text-gray-500 mb-1">Time Remaining</div>
<div class="flex items-end justify-between">
<div class="text-xl font-semibold text-gray-800">28 days</div>
<div class="text-sm text-yellow-600 flex items-center">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-time-line"></i>
</div>
<span>On schedule</span>
</div>
</div>
</div>
</div>
<div class="mb-6">
<div class="flex justify-between items-center mb-2">
<div class="text-sm font-medium text-gray-700">Overall Progress</div>
<div class="text-sm font-medium text-primary">54%</div>
</div>
<div class="w-full bg-gray-200 rounded-full h-2.5">
<div class="bg-primary h-2.5 rounded-full" style="width: 54%"></div>
</div>
</div>
<div class="grid grid-cols-2 gap-6">
<div>
<div class="flex justify-between items-center mb-4">
<h3 class="text-sm font-medium text-gray-700">Weekly Progress</h3>
<button class="text-xs text-gray-500 hover:text-gray-700">
Last 4 weeks
</button>
</div>
<div id="weeklyProgressChart" class="h-64"></div>
</div>
<div>
<div class="flex justify-between items-center mb-4">
<h3 class="text-sm font-medium text-gray-700">Task Status</h3>
<button class="text-xs text-gray-500 hover:text-gray-700">
View all
</button>
</div>
<div id="taskStatusChart" class="h-64"></div>
</div>
</div>
</div>
<!-- Team Members Panel removed per user request -->
<!--
<div class="col-span-12 lg:col-span-4 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-4">
<h2 class="text-lg font-semibold text-gray-800">Team Members</h2>
<button class="text-sm text-primary hover:text-indigo-700 whitespace-nowrap">View all</button>
</div>
<div class="space-y-4 mb-6">
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20young%20business%20woman%20with%20short%20brown%20hair%2C%20neutral%20expression%2C%20business%20attire%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=1&orientation=squarish" alt="Emily Parker" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">Emily Parker</div>
<div class="text-xs text-gray-500">Project Manager</div>
<div id="teamWorkloadChart" class="h-48"></div>
</div>
</div>
-->
<!-- <button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20an%20asian%20man%20in%20his%2030s%2C%20short%20black%20hair%2C%20wearing%20business%20casual%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=2&orientation=squarish" alt="David Chen" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">David Chen</div>
<div class="text-xs text-gray-500">Lead Designer</div>
</div>
</div>
<button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20black%20woman%20in%20her%2020s%20with%20natural%20hair%2C%20wearing%20business%20casual%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=3&orientation=squarish" alt="Sophia Williams" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-gray-300 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">Sophia Williams</div>
<div class="text-xs text-gray-500">Content Strategist</div>
</div>
</div>
<button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20caucasian%20man%20in%20his%2040s%20with%20glasses%20and%20short%20brown%20hair%2C%20wearing%20business%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=4&orientation=squarish" alt="Michael Johnson" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">Michael Johnson</div>
<div class="text-xs text-gray-500">SEO Specialist</div>
</div>
</div>
<button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
<div class="flex items-center justify-between">
<div class="flex items-center space-x-3">
<div class="relative">
<div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20latina%20woman%20in%20her%2030s%20with%20long%20dark%20hair%2C%20wearing%20business%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=5&orientation=squarish" alt="Isabella Rodriguez" class="w-full h-full object-cover object-top">
</div>
<div class="absolute bottom-0 right-0 w-3 h-3 bg-yellow-500 border-2 border-white rounded-full"></div>
</div>
<div>
<div class="font-medium text-gray-800">Isabella Rodriguez</div>
<div class="text-xs text-gray-500">Social Media Manager</div>
</div>
</div>
<button class="p-1.5 rounded-full hover:bg-gray-100 !rounded-button">
<div class="w-4 h-4 flex items-center justify-center">
<i class="ri-message-2-line text-gray-500"></i>
</div>
</button>
</div>
</div>
<div>
<div class="flex justify-between items-center mb-4">
<h3 class="text-sm font-medium text-gray-700">Team Workload</h3>
<button class="text-xs text-gray-500 hover:text-gray-700">
This week
</button>
 </div> -->
</div>
<!-- Progress Tracking Section -->
<div class="col-span-12 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Project Timeline</h2>
<div class="flex items-center space-x-2">
<div class="flex items-center space-x-1">
<div class="w-3 h-3 bg-blue-400 rounded-full"></div>
<span class="text-xs text-gray-600">On track</span>
</div>
<div class="flex items-center space-x-1">
<div class="w-3 h-3 bg-yellow-400 rounded-full"></div>
<span class="text-xs text-gray-600">At risk</span>
</div>
<div class="flex items-center space-x-1">
<div class="w-3 h-3 bg-red-400 rounded-full"></div>
<span class="text-xs text-gray-600">Delayed</span>
</div>
</div>
</div>
<div class="overflow-x-auto custom-scrollbar">
<div class="gantt-chart min-w-[1000px]">
<div class="flex mb-2">
<div class="w-1/4 pr-4 font-medium text-gray-700">Task</div>
<div class="w-3/4 grid grid-cols-12 gap-0">
<div class="text-xs text-gray-500 text-center">Week 1</div>
<div class="text-xs text-gray-500 text-center">Week 2</div>
<div class="text-xs text-gray-500 text-center">Week 3</div>
<div class="text-xs text-gray-500 text-center">Week 4</div>
<div class="text-xs text-gray-500 text-center">Week 5</div>
<div class="text-xs text-gray-500 text-center">Week 6</div>
<div class="text-xs text-gray-500 text-center">Week 7</div>
<div class="text-xs text-gray-500 text-center">Week 8</div>
<div class="text-xs text-gray-500 text-center">Week 9</div>
<div class="text-xs text-gray-500 text-center">Week 10</div>
<div class="text-xs text-gray-500 text-center">Week 11</div>
<div class="text-xs text-gray-500 text-center">Week 12</div>
</div>
</div>
<div class="flex items-center mb-3">
<div class="w-1/4 pr-4">
<div class="text-sm font-medium text-gray-800">Research & Planning</div>
<div class="text-xs text-gray-500">Emily Parker</div>
</div>
<div class="w-3/4 grid grid-cols-12 gap-0 relative">
<div class="task task-on-track col-span-2 col-start-1"></div>
</div>
</div>
<div class="flex items-center mb-3">
<div class="w-1/4 pr-4">
<div class="text-sm font-medium text-gray-800">Content Creation</div>
<div class="text-xs text-gray-500">Sophia Williams</div>
</div>
<div class="w-3/4 grid grid-cols-12 gap-0 relative">
<div class="task task-on-track col-span-3 col-start-2"></div>
</div>
</div>
<div class="flex items-center mb-3">
<div class="w-1/4 pr-4">
<div class="text-sm font-medium text-gray-800">Design Assets</div>
<div class="text-xs text-gray-500">David Chen</div>
</div>
<div class="w-3/4 grid grid-cols-12 gap-0 relative">
<div class="task task-at-risk col-span-3 col-start-3"></div>
</div>
</div>
<div class="flex items-center mb-3">
<div class="w-1/4 pr-4">
<div class="text-sm font-medium text-gray-800">SEO Optimization</div>
<div class="text-xs text-gray-500">Michael Johnson</div>
</div>
<div class="w-3/4 grid grid-cols-12 gap-0 relative">
<div class="task task-on-track col-span-2 col-start-5"></div>
</div>
</div>
<div class="flex items-center mb-3">
<div class="w-1/4 pr-4">
<div class="text-sm font-medium text-gray-800">Social Media Strategy</div>
<div class="text-xs text-gray-500">Isabella Rodriguez</div>
</div>
<div class="w-3/4 grid grid-cols-12 gap-0 relative">
<div class="task task-delayed col-span-3 col-start-6"></div>
</div>
</div>
<div class="flex items-center mb-3">
<div class="w-1/4 pr-4">
<div class="text-sm font-medium text-gray-800">Campaign Launch</div>
<div class="text-xs text-gray-500">Team</div>
</div>
<div class="w-3/4 grid grid-cols-12 gap-0 relative">
<div class="task task-on-track col-span-2 col-start-9"></div>
</div>
</div>
<div class="flex items-center mb-3">
<div class="w-1/4 pr-4">
<div class="text-sm font-medium text-gray-800">Performance Analysis</div>
<div class="text-xs text-gray-500">Emily Parker</div>
</div>
<div class="w-3/4 grid grid-cols-12 gap-0 relative">
<div class="task task-on-track col-span-2 col-start-11"></div>
</div>
</div>
</div>
</div>
</div>
<!-- Milestones Widget -->
<div class="col-span-12 lg:col-span-6 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Key Milestones</h2>
<button class="text-sm text-primary hover:text-indigo-700 whitespace-nowrap">View all</button>
</div>
<div class="space-y-6 relative">
<div class="absolute left-4 top-1 bottom-0 timeline-line"></div>
<div class="flex">
<div class="mr-4 relative">
<div class="timeline-dot milestone-completed mt-1"></div>
</div>
<div class="flex-1">
<div class="flex justify-between items-start">
<div>
<h3 class="text-sm font-medium text-gray-800">Project Kickoff</h3>
<p class="text-xs text-gray-500 mt-1">Campaign goals and timeline established</p>
</div>
<div class="text-xs text-gray-500">Apr 15, 2025</div>
</div>
<div class="flex items-center mt-2">
<div class="text-xs px-2 py-0.5 bg-green-100 text-green-800 rounded-full">Completed</div>
</div>
</div>
</div>
<div class="flex">
<div class="mr-4 relative">
<div class="timeline-dot milestone-completed mt-1"></div>
</div>
<div class="flex-1">
<div class="flex justify-between items-start">
<div>
<h3 class="text-sm font-medium text-gray-800">Content Strategy Approval</h3>
<p class="text-xs text-gray-500 mt-1">Content plan and messaging approved by stakeholders</p>
</div>
<div class="text-xs text-gray-500">Apr 28, 2025</div>
</div>
<div class="flex items-center mt-2">
<div class="text-xs px-2 py-0.5 bg-green-100 text-green-800 rounded-full">Completed</div>
</div>
</div>
</div>
<div class="flex">
<div class="mr-4 relative">
<div class="timeline-dot milestone-upcoming mt-1"></div>
</div>
<div class="flex-1">
<div class="flex justify-between items-start">
<div>
<h3 class="text-sm font-medium text-gray-800">Design Assets Delivery</h3>
<p class="text-xs text-gray-500 mt-1">All campaign visuals and creative assets completed</p>
</div>
<div class="text-xs text-gray-500">May 15, 2025</div>
</div>
<div class="flex items-center mt-2">
<div class="text-xs px-2 py-0.5 bg-yellow-100 text-yellow-800 rounded-full">In Progress</div>
</div>
</div>
</div>
<div class="flex">
<div class="mr-4 relative">
<div class="timeline-dot milestone-upcoming mt-1"></div>
</div>
<div class="flex-1">
<div class="flex justify-between items-start">
<div>
<h3 class="text-sm font-medium text-gray-800">Campaign Launch</h3>
<p class="text-xs text-gray-500 mt-1">Official launch across all planned channels</p>
</div>
<div class="text-xs text-gray-500">Jun 01, 2025</div>
</div>
<div class="flex items-center mt-2">
<div class="text-xs px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full">Upcoming</div>
</div>
</div>
</div>
<div class="flex">
<div class="mr-4 relative">
<div class="timeline-dot milestone-upcoming mt-1"></div>
</div>
<div class="flex-1">
<div class="flex justify-between items-start">
<div>
<h3 class="text-sm font-medium text-gray-800">Mid-Campaign Review</h3>
<p class="text-xs text-gray-500 mt-1">Performance assessment and strategy adjustments</p>
</div>
<div class="text-xs text-gray-500">Jun 15, 2025</div>
</div>
<div class="flex items-center mt-2">
<div class="text-xs px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full">Upcoming</div>
</div>
</div>
</div>
</div>
</div>
<!-- Quick Access Section -->
<div class="col-span-12 lg:col-span-6 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Quick Access</h2>
<button class="text-sm text-primary hover:text-indigo-700 whitespace-nowrap">Customize</button>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-blue-600">
<i class="ri-file-list-3-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Documents</div>
<div class="text-xs text-gray-500">24 files</div>
</div>
</div>
</div>
<div class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-purple-600">
<i class="ri-calendar-2-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Meetings</div>
<div class="text-xs text-gray-500">3 upcoming</div>
</div>
</div>
</div>
<div class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-red-600">
<i class="ri-alert-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Risk Register</div>
<div class="text-xs text-gray-500">2 high priority</div>
</div>
</div>
</div>
<div class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-green-600">
<i class="ri-money-dollar-circle-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Payment Status</div>
<div class="text-xs text-gray-500">1 of 3 payments received</div>
</div>
</div>
</div>
<div class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-yellow-600">
<i class="ri-stack-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Resources</div>
<div class="text-xs text-gray-500">12 items</div>
</div>
</div>
</div>
<div class="bg-gray-50 p-4 rounded hover:bg-gray-100 transition-colors cursor-pointer">
<div class="flex items-center space-x-3">
<div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
<div class="w-5 h-5 flex items-center justify-center text-indigo-600">
<i class="ri-bar-chart-2-line"></i>
</div>
</div>
<div>
<div class="font-medium text-gray-800">Reports</div>
<div class="text-xs text-gray-500">Generate reports</div>
</div>
</div>
</div>
</div>
</div>
@php
$projectId = $project['id'] ?? $project['project_id'] ?? null;
@endphp
@if($projectId)
<div class="col-span-12 lg:col-span-6 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-4">
<div>
<h2 class="text-lg font-semibold text-gray-800">Project Files</h2>
<p class="text-sm text-gray-500">Any file type, up to 100MB each.</p>
</div>
</div>
<form action="{{ route('projects.files.store', $projectId) }}" method="POST" enctype="multipart/form-data" class="mb-4">
@csrf
<div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3 space-y-3 sm:space-y-0">
<input type="file" name="file" required class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-indigo-600">
<button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Upload</button>
</div>
<p class="text-xs text-gray-500 mt-1">Max size: 100MB. All file types allowed.</p>
@error('file')
<p class="text-sm text-red-600 mt-1">{{ $message }}</p>
@enderror
</form>
<div class="divide-y divide-gray-100">
@forelse(($projectFiles ?? []) as $file)
<div class="py-3 flex items-center justify-between">
<div class="flex-1 min-w-0">
<div class="text-sm font-medium text-gray-800 truncate">{{ $file->original_name }}</div>
<div class="text-xs text-gray-500 flex items-center space-x-3">
<span>{{ number_format($file->size / 1048576, 2) }} MB</span>
<span>•</span>
<span>{{ $file->created_at?->format('Y-m-d H:i') }}</span>
</div>
</div>
<div class="flex items-center space-x-2 flex-shrink-0">
<a href="{{ route('projects.files.download', [$projectId, $file->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Download</a>
<form action="{{ route('projects.files.destroy', [$projectId, $file->id]) }}" method="POST" onsubmit="return confirm('Delete this file?');">
@csrf
@method('DELETE')
<button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
</form>
</div>
</div>
@empty
<p class="text-sm text-gray-500 py-2">No files uploaded yet.</p>
@endforelse
</div>
</div>
@endif
<!-- Activity Feed -->
<div class="col-span-12 bg-white rounded shadow-sm p-6">
<div class="flex justify-between items-center mb-6">
<h2 class="text-lg font-semibold text-gray-800">Recent Activity</h2>
<div class="flex items-center space-x-2">
<button class="px-3 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded-full hover:bg-gray-200 transition-colors whitespace-nowrap">All</button>
<button class="px-3 py-1 text-xs font-medium text-gray-500 rounded-full hover:bg-gray-100 transition-colors whitespace-nowrap">Comments</button>
<button class="px-3 py-1 text-xs font-medium text-gray-500 rounded-full hover:bg-gray-100 transition-colors whitespace-nowrap">Tasks</button>
<button class="px-3 py-1 text-xs font-medium text-gray-500 rounded-full hover:bg-gray-100 transition-colors whitespace-nowrap">Files</button>
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
<span class="text-gray-600 text-sm"> updated the project status to </span>
<span class="font-medium text-green-600">On Track</span>
</div>
<div class="text-xs text-gray-500">10 minutes ago</div>
</div>
<div class="mt-2 text-sm text-gray-600">
"The project is progressing as planned. All team members are aligned with their tasks for the week."
</div>
</div>
</div>
<div class="flex space-x-3">
<div class="w-8 h-8 rounded-full bg-gray-200 overflow-hidden flex-shrink-0">
<img src="https://readdy.ai/api/search-image?query=professional%20headshot%20of%20a%20latina%20woman%20in%20her%2030s%20with%20long%20dark%20hair%2C%20wearing%20business%20attire%2C%20neutral%20expression%2C%20high%20quality%20professional%20photo&width=100&height=100&seq=5&orientation=squarish" alt="Isabella Rodriguez" class="w-full h-full object-cover object-top">
</div>
<div class="flex-1">
<div class="flex justify-between">
<div>
<span class="font-medium text-gray-800">Isabella Rodriguez</span>
<span class="text-gray-600 text-sm"> uploaded </span>
<span class="font-medium text-blue-600">3 new files</span>
</div>
<div class="text-xs text-gray-500">1 hour ago</div>
</div>
<div class="mt-2 flex space-x-2">
<div class="px-3 py-2 bg-gray-50 rounded text-xs flex items-center space-x-1">
<div class="w-4 h-4 flex items-center justify-center text-blue-500">
<i class="ri-file-text-line"></i>
</div>
<span>social_media_plan.pdf</span>
</div>
<div class="px-3 py-2 bg-gray-50 rounded text-xs flex items-center space-x-1">
<div class="w-4 h-4 flex items-center justify-center text-green-500">
<i class="ri-file-excel-2-line"></i>
</div>
<span>content_calendar.xlsx</span>
</div>
<div class="px-3 py-2 bg-gray-50 rounded text-xs flex items-center space-x-1">
<div class="w-4 h-4 flex items-center justify-center text-red-500">
<i class="ri-file-pdf-line"></i>
</div>
<span>audience_analysis.pdf</span>
</div>
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
<span class="text-gray-600 text-sm"> commented on </span>
<span class="font-medium text-gray-800">Design Assets</span>
</div>
<div class="text-xs text-gray-500">3 hours ago</div>
</div>
<div class="mt-2 text-sm text-gray-600">
"I've completed the initial mockups for the campaign. There are a few revisions needed based on the brand guidelines. I'll have the updated versions ready by tomorrow."
</div>
<div class="mt-2 p-3 bg-gray-50 rounded-md">
<div class="flex items-center justify-between">
<div class="text-xs font-medium text-gray-700">Design Assets Task</div>
<div class="text-xs text-yellow-600">At Risk</div>
</div>
<div class="mt-1 text-xs text-gray-500">Due: May 15, 2025</div>
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
<span class="text-gray-600 text-sm"> completed </span>
<span class="font-medium text-gray-800">Content Draft Review</span>
</div>
<div class="text-xs text-gray-500">Yesterday</div>
</div>
<div class="mt-2 text-sm text-gray-600">
"All content drafts have been reviewed and approved. Ready for final formatting and design integration."
</div>
</div>
</div>
</div>
</div>
</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
// Weekly Progress Chart
const weeklyProgressChart = echarts.init(document.getElementById('weeklyProgressChart'));
const weeklyProgressOption = {
animation: false,
tooltip: {
trigger: 'axis',
backgroundColor: 'rgba(255, 255, 255, 0.9)',
borderColor: '#e5e7eb',
borderWidth: 1,
textStyle: {
color: '#1f2937'
}
},
grid: {
top: 10,
right: 10,
bottom: 20,
left: 40
},
xAxis: {
type: 'category',
data: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
axisLine: {
lineStyle: {
color: '#e5e7eb'
}
},
axisLabel: {
color: '#1f2937'
}
},
yAxis: {
type: 'value',
min: 0,
max: 100,
axisLine: {
show: false
},
axisLabel: {
color: '#1f2937',
formatter: '{value}%'
},
splitLine: {
lineStyle: {
color: '#f3f4f6'
}
}
},
series: [
{
name: 'Planned',
type: 'line',
smooth: true,
symbol: 'none',
data: [15, 30, 45, 60],
lineStyle: {
color: 'rgba(141, 211, 199, 1)',
width: 3
},
areaStyle: {
color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
{ offset: 0, color: 'rgba(141, 211, 199, 0.3)' },
{ offset: 1, color: 'rgba(141, 211, 199, 0.1)' }
])
}
},
{
name: 'Actual',
type: 'line',
smooth: true,
symbol: 'none',
data: [12, 25, 42, 54],
lineStyle: {
color: 'rgba(87, 181, 231, 1)',
width: 3
},
areaStyle: {
color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
{ offset: 0, color: 'rgba(87, 181, 231, 0.3)' },
{ offset: 1, color: 'rgba(87, 181, 231, 0.1)' }
])
}
}
]
};
weeklyProgressChart.setOption(weeklyProgressOption);
// Task Status Chart
const taskStatusChart = echarts.init(document.getElementById('taskStatusChart'));
const taskStatusOption = {
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
name: 'Task Status',
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
{ value: 42, name: 'Completed', itemStyle: { color: 'rgba(87, 181, 231, 1)' } },
{ value: 21, name: 'In Progress', itemStyle: { color: 'rgba(141, 211, 199, 1)' } },
{ value: 10, name: 'At Risk', itemStyle: { color: 'rgba(251, 191, 114, 1)' } },
{ value: 5, name: 'Delayed', itemStyle: { color: 'rgba(252, 141, 98, 1)' } }
]
}
]
};
taskStatusChart.setOption(taskStatusOption);
// Resize charts when window size changes
window.addEventListener('resize', function() {
weeklyProgressChart.resize();
taskStatusChart.resize();
});
});
</script>
</body>
</html>