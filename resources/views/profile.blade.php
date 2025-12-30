<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Profile</title>
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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile -->
        <div class="col-span-1 lg:col-span-2 bg-white rounded shadow-sm p-6">
            <h1 class="text-2xl font-semibold text-gray-800 mb-4">My Profile</h1>

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

            <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Leave blank to keep current">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary" placeholder="Confirm new password">
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600 transition-colors">Save Changes</button>
                </div>
            </form>
        </div>

        <!-- User Management (PM only) -->
        @if(strtolower($user->role ?? '') === 'project manager')
        <div class="col-span-1 bg-white rounded shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">User Management</h2>

            <form action="{{ route('users.store') }}" method="POST" class="space-y-3 mb-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">User ID</label>
                    <input type="text" name="user_id" value="{{ old('user_id') }}" required class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" required class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                    <select name="role" class="w-full rounded-button p-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary" required>
                        <option value="Project Manager">Project Manager</option>
                        <option value="Technical Manager">Technical Manager</option>
                        <option value="Finance">Finance</option>
                        <option value="Authority">Authority</option>
                        <option value="Supply Chain">Supply Chain</option>
                        <option value="Member">Member</option>
                    </select>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-button hover:bg-indigo-600 transition-colors">Add User</button>
                </div>
            </form>

            <div class="space-y-3">
                @foreach($users as $u)
                <div class="border border-gray-200 rounded p-3 flex items-center justify-between">
                    <div>
                        <div class="font-medium text-gray-800">{{ $u->name }}</div>
                        <div class="text-xs text-gray-500">{{ $u->email }}</div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <form action="{{ route('users.updateRole', $u->user_id) }}" method="POST" class="flex items-center space-x-2">
                            @csrf
                            @method('PUT')
                            <select name="role" class="text-sm border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-primary">
                                <option value="Project Manager" {{ $u->role === 'Project Manager' ? 'selected' : '' }}>Project Manager</option>
                                <option value="Technical Manager" {{ $u->role === 'Technical Manager' ? 'selected' : '' }}>Technical Manager</option>
                                <option value="Finance" {{ $u->role === 'Finance' ? 'selected' : '' }}>Finance</option>
                                <option value="Authority" {{ $u->role === 'Authority' ? 'selected' : '' }}>Authority</option>
                                <option value="Supply Chain" {{ $u->role === 'Supply Chain' ? 'selected' : '' }}>Supply Chain</option>
                                <option value="Member" {{ $u->role === 'Member' ? 'selected' : '' }}>Member</option>
                            </select>
                            <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800">Update</button>
                        </form>
                        @if($u->user_id !== auth()->id())
                        <form action="{{ route('users.destroy', $u->user_id) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</main>
</body>
</html>

