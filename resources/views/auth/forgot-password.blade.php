@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="w-full max-w-md bg-white rounded-lg shadow-sm p-8">
        <h2 class="text-2xl font-bold text-center text-brand mb-4">Reset Password</h2>
        <p class="text-center text-gray-600 text-sm mb-8">Enter your email to receive a password reset link</p>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-600 text-sm">{{ session('status') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-red-600 text-sm">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-900">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand focus:border-brand focus:outline-none">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="w-full bg-brand text-white font-medium py-2 rounded-lg hover:bg-brand-dark transition">
                Send Reset Link
            </button>
        </form>

        <p class="text-center text-sm text-gray-600 mt-6">
            Remember your password? <a href="{{ route('login') }}" class="text-brand hover:text-brand-dark font-medium">Sign in</a>
        </p>
    </div>
</div>
@endsection
