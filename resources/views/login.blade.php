<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EC Newenergie</title>
    <script src="https://cdn.tailwindcss.com/3.4.16"></script>
    <script>tailwind.config={theme:{extend:{colors:{primary:'#4f46e5',secondary:'#6366f1'},fontFamily:{sans:['Inter','sans-serif']}}}}</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
        <div class="text-center mb-8">
            <img src="{{ asset('images/logo ecn.png') }}" alt="EC Newenergie" class="h-12 mx-auto mb-4">
            <h2 class="text-2xl font-bold text-gray-900">Welcome Back</h2>
            <p class="text-sm text-gray-500">Sign in to access the Project Tracker</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-600 text-sm rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                       placeholder="you@ecnewenergie.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" required 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary outline-none transition-all"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between text-sm">
                <label class="flex items-center text-gray-600">
                    <input type="checkbox" name="remember" class="mr-2 rounded text-primary focus:ring-primary">
                    Remember me
                </label>
                <a href="#" class="text-primary hover:text-indigo-700 font-medium">Forgot password?</a>
            </div>

            <button type="submit" 
                    class="w-full bg-primary hover:bg-indigo-700 text-white font-semibold py-2.5 rounded-lg transition-colors shadow-sm">
                Sign In
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} EC Newenergie. All rights reserved.
        </div>
    </div>
</body>
</html>