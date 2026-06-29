<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | Henex Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 font-sans">

<div x-data="{ sidebarOpen: true }" class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'w-56' : 'w-0'" class="bg-neutral-950 text-white flex-shrink-0 overflow-hidden transition-all duration-300 flex flex-col">
        <div class="px-5 py-4 border-b border-white/10">
            <span class="text-brand font-bold text-lg">HENEX</span>
            <span class="text-gray-400 text-xs ml-1">Admin</span>
        </div>
        <nav class="flex-1 py-4 px-3 space-y-1 text-sm overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 font-semibold' : '' }}">📊 Dashboard</a>
            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.products.*') ? 'bg-white/10 font-semibold' : '' }}">📦 Products</a>
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.categories.*') ? 'bg-white/10 font-semibold' : '' }}">🗂 Categories</a>
            <a href="{{ route('admin.resellers.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.resellers.*') ? 'bg-white/10 font-semibold' : '' }}">🏪 Resellers</a>
            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.articles.*') ? 'bg-white/10 font-semibold' : '' }}">📰 Articles</a>
            <a href="{{ route('admin.slides.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.slides.*') ? 'bg-white/10 font-semibold' : '' }}">🖼 Slides</a>
            <a href="{{ route('admin.inquiries.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.inquiries.*') ? 'bg-white/10 font-semibold' : '' }}">💬 Inquiries</a>
            @role('super_admin')
            <div class="pt-3 border-t border-white/10 mt-3 space-y-1">
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.settings') ? 'bg-white/10 font-semibold' : '' }}">⚙️ Settings</a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10 {{ request()->routeIs('admin.users.*') ? 'bg-white/10 font-semibold' : '' }}">👥 Users</a>
            </div>
            @endrole
        </nav>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" target="_blank" class="text-xs text-gray-500 hover:text-brand transition">← View site</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-gray-500 hover:text-red-500 transition">Logout</button>
                </form>
            </div>
        </header>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
