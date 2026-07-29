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
    <aside :class="sidebarOpen ? 'w-60' : 'w-0'" class="bg-neutral-950 text-white flex-shrink-0 overflow-hidden transition-all duration-300 flex flex-col">
        <div class="px-5 py-4 border-b border-white/10 flex items-baseline gap-1.5">
            <span class="text-brand font-bold text-lg tracking-tight">HENEX</span>
            <span class="text-gray-500 text-xs">Admin</span>
        </div>
        <nav class="flex-1 py-4 px-3 space-y-0.5 text-sm overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg border-l-2 transition {{ request()->routeIs('admin.dashboard') ? 'border-brand bg-white/10 text-white font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">
                <x-admin.icon name="dashboard" class="w-[18px] h-[18px] shrink-0" /> Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg border-l-2 transition {{ request()->routeIs('admin.products.*') ? 'border-brand bg-white/10 text-white font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">
                <x-admin.icon name="products" class="w-[18px] h-[18px] shrink-0" /> Products
            </a>
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg border-l-2 transition {{ request()->routeIs('admin.categories.*') ? 'border-brand bg-white/10 text-white font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">
                <x-admin.icon name="categories" class="w-[18px] h-[18px] shrink-0" /> Categories
            </a>
            <a href="{{ route('admin.resellers.index') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg border-l-2 transition {{ request()->routeIs('admin.resellers.*') ? 'border-brand bg-white/10 text-white font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">
                <x-admin.icon name="resellers" class="w-[18px] h-[18px] shrink-0" /> Resellers
            </a>
            <a href="{{ route('admin.articles.index') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg border-l-2 transition {{ request()->routeIs('admin.articles.*') ? 'border-brand bg-white/10 text-white font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">
                <x-admin.icon name="articles" class="w-[18px] h-[18px] shrink-0" /> Articles
            </a>
            <a href="{{ route('admin.slides.index') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg border-l-2 transition {{ request()->routeIs('admin.slides.*') ? 'border-brand bg-white/10 text-white font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">
                <x-admin.icon name="slides" class="w-[18px] h-[18px] shrink-0" /> Slides
            </a>
            <a href="{{ route('admin.inquiries.index') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg border-l-2 transition {{ request()->routeIs('admin.inquiries.*') ? 'border-brand bg-white/10 text-white font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">
                <x-admin.icon name="inquiries" class="w-[18px] h-[18px] shrink-0" /> Inquiries
            </a>
            @role('super_admin')
            <div class="pt-3 mt-3 border-t border-white/10 space-y-0.5">
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg border-l-2 transition {{ request()->routeIs('admin.settings') ? 'border-brand bg-white/10 text-white font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <x-admin.icon name="settings" class="w-[18px] h-[18px] shrink-0" /> Settings
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 pl-3 pr-3 py-2 rounded-lg border-l-2 transition {{ request()->routeIs('admin.users.*') ? 'border-brand bg-white/10 text-white font-medium' : 'border-transparent text-gray-400 hover:text-white hover:bg-white/5' }}">
                    <x-admin.icon name="users" class="w-[18px] h-[18px] shrink-0" /> Users
                </a>
            </div>
            @endrole
        </nav>
    </aside>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        {{-- Topbar --}}
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between flex-shrink-0">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="flex items-center gap-2">
                <a href="{{ route('home') }}" target="_blank"
                   class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-brand px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                    <x-admin.icon name="external" class="w-3.5 h-3.5" /> View site
                </a>
                <div class="w-px h-5 bg-gray-200 mx-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 hover:text-red-600 px-3 py-1.5 rounded-lg hover:bg-red-50 transition">
                        <x-admin.icon name="logout" class="w-3.5 h-3.5" /> Logout
                    </button>
                </form>
            </div>
        </header>

        {{-- Toast notifications --}}
        <div x-data="{
                toasts: [],
                add(msg, type = 'success') {
                    const id = Date.now();
                    this.toasts.push({ id, msg, type });
                    setTimeout(() => this.remove(id), 3500);
                },
                remove(id) { this.toasts = this.toasts.filter(t => t.id !== id); },
                init() {
                    @if(session('success')) this.add('{{ addslashes(session('success')) }}', 'success'); @endif
                    @if(session('error'))   this.add('{{ addslashes(session('error')) }}', 'error'); @endif
                }
             }"
             x-on:toast.window="add($event.detail.message, $event.detail.type ?? 'success')"
             class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none">
            <template x-for="t in toasts" :key="t.id">
                <div x-show="true"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-x-4"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-end="opacity-0"
                     :class="t.type === 'error' ? 'bg-red-600' : t.type === 'warning' ? 'bg-yellow-500' : 'bg-green-600'"
                     class="pointer-events-auto flex items-center gap-3 px-5 py-3 rounded-xl text-white text-sm font-medium shadow-lg min-w-52">
                    <span x-text="t.msg" class="flex-1"></span>
                    <button @click="remove(t.id)" class="opacity-70 hover:opacity-100 text-lg leading-none">&times;</button>
                </div>
            </template>
        </div>

        {{-- Content --}}
        <main class="flex-1 overflow-y-auto p-6">
            {{ $slot }}
        </main>
    </div>
</div>

@livewireScripts
</body>
</html>
