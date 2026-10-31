<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Henex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans">

<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <span class="text-2xl font-extrabold text-brand tracking-tight">HENEX</span>
            <h2 class="text-lg font-semibold text-gray-900 mt-2">Admin Login</h2>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-red-600 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-900">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand focus:outline-none">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-900">Password</label>
                <input type="password" id="password" name="password" required
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand focus:outline-none">
                @error('password')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" id="remember" name="remember" value="1"
                    class="w-4 h-4 text-brand bg-gray-100 border-gray-300 rounded focus:ring-brand">
                <label for="remember" class="ml-2 text-sm text-gray-600">Remember me</label>
            </div>

            <button type="submit" class="w-full bg-brand text-white font-medium py-2 rounded-lg hover:bg-brand-dark transition">
                Sign In
            </button>
        </form>

    </div>
</div>

</body>
</html>
