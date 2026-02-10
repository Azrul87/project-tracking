<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project Materials</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},borderRadius:{'none':'0px','sm':'4px',DEFAULT:'8px','md':'12px','lg':'16px','xl':'20px','2xl':'24px','3xl':'32px','full':'9999px','button':'8px'}}}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.6.0/remixicon.min.css">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen">
@include('partials.navigation')

<div class="container mx-auto px-6 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <nav class="flex mb-4" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('inventory') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                            <i class="ri-table-line mr-2"></i>
                            Material Matrix
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <i class="ri-arrow-right-s-line text-gray-400"></i>
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">Edit Materials</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900">Edit Project Materials</h1>
            <p class="text-gray-600 mt-2">Update material quantities for <span class="font-semibold text-primary">{{ $project->client->client_name ?? $project->project_id }}</span></p>
        </div>
        <div>
            <a href="{{ route('inventory') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200">
                Cancel
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('projects.materials.update', $project->project_id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($materialFields as $field => $label)
                        @php
                            // Find the material in the project's materials collection
                            $pivotMaterial = $project->materials->firstWhere('code', $field);
                            $currentValue = $pivotMaterial ? $pivotMaterial->pivot->quantity : 0;
                        @endphp
                        <div class="relative">
                            <label for="{{ $field }}" class="block mb-2 text-sm font-medium text-gray-700">{{ $label }}</label>
                            <div class="relative">
                                <input type="number" 
                                       id="{{ $field }}" 
                                       name="{{ $field }}" 
                                       value="{{ old($field, $currentValue) }}"
                                       min="0"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pl-4"
                                       placeholder="0">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <span class="text-gray-400 text-xs">qty</span>
                                </div>
                            </div>
                            @error($field)
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-gray-50 px-8 py-5 flex items-center justify-end border-t border-gray-200">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 focus:outline-none flex items-center">
                    <i class="ri-save-line mr-2"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
