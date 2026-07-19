<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'HENEX Uzbekistan — ' . __('site.official_distributor'))</title>

    {{-- Page-specific meta (OG, description, canonical, hreflang) --}}
    @hasSection('meta')
        @yield('meta')
    @else
        <meta name="description" content="Official distributor of HENEX barcode scanners in Uzbekistan. Official warranty, technical support, fast delivery.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="HENEX Uzbekistan — Official Barcode Scanner Distributor">
        <meta property="og:description" content="Official distributor of HENEX barcode scanners in Uzbekistan. Warranty, technical support, fast delivery.">
        <meta property="og:image" content="{{ asset('images/henex-og.jpg') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:site_name" content="HENEX Uzbekistan">
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ url()->current() }}">
        <meta property="twitter:title" content="HENEX Uzbekistan — Official Barcode Scanner Distributor">
        <meta property="twitter:description" content="Official distributor of HENEX barcode scanners in Uzbekistan.">
        <meta property="twitter:image" content="{{ asset('images/henex-og.jpg') }}">
        <link rel="canonical" href="{{ url()->current() }}">
    @endif

    {{-- Page-specific structured data (Product, Article, etc.) --}}
    @yield('schema')

    @include('partials.seo-meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white text-neutral-950 font-sans" x-data="{ mobileOpen: false }">

{{-- ─── TOP UTILITY BAR ─────────────────────────────────────────────────── --}}
<div class="bg-neutral-900 text-gray-300 text-xs py-2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
        <div class="flex items-center gap-5">
            <a href="tel:+998901234567" class="flex items-center gap-1.5 hover:text-white transition">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"></path></svg>
                +998 90 123 45 67
            </a>
            <a href="mailto:info@henex.uz" class="hidden sm:flex items-center gap-1.5 hover:text-white transition">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path></svg>
                info@henex.uz
            </a>
        </div>

        {{-- Locale switcher --}}
        <div class="flex items-center gap-3">
            @foreach(['uz' => "O'z", 'ru' => 'Рус', 'en' => 'Eng'] as $code => $label)
                <form action="{{ route('locale.set', $code) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="text-xs font-semibold {{ app()->getLocale() === $code ? 'text-brand' : 'hover:text-white' }} transition cursor-pointer bg-transparent border-0 p-0">
                        {{ $label }}
                    </button>
                </form>
                @if (!$loop->last)<span class="text-gray-600">|</span>@endif
            @endforeach
        </div>
    </div>
</div>

{{-- ─── MAIN HEADER ──────────────────────────────────────────────────────── --}}
<header class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
            <span class="text-2xl font-extrabold text-brand tracking-tight">HENEX</span>
            <span class="hidden sm:block text-xs text-gray-500 leading-tight">Uzbekistan<br>Official Distributor</span>
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden lg:flex items-center gap-1">

            <a href="{{ route('home') }}"
               class="px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('home') ? 'text-brand' : 'text-gray-700 hover:text-brand' }} transition">
                {{ __('site.nav.home') }}
            </a>

            {{-- Products mega-dropdown --}}
            <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('products.*') ? 'text-brand' : 'text-gray-700 hover:text-brand' }} transition">
                    {{ __('site.nav.products') }}
                    <svg class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute top-full left-0 w-64 z-50 pt-1">
                    <div class="bg-white rounded-xl shadow-xl border border-gray-100 py-2">
                        <a href="{{ route('products.index') }}"
                           class="block px-5 py-2.5 text-sm font-semibold text-brand hover:bg-orange-50 transition border-b border-gray-100 mb-1">
                            {{ __('site.nav.all_products') ?? 'All Products' }} →
                        </a>
                        @foreach($categories ?? [] as $category)
                            <a href="{{ route('products.category', $category->slug) }}"
                               class="block px-5 py-2 text-sm text-gray-700 hover:text-brand hover:bg-gray-50 transition">
                                {{ $category->getTranslation('name', app()->getLocale()) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Industries dropdown --}}
            <div class="relative" x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                <button class="flex items-center gap-1 px-4 py-2 text-sm font-medium rounded-md text-gray-700 hover:text-brand transition">
                    {{ __('site.nav.industries') ?? 'Industries' }}
                    <svg class="w-4 h-4 transition" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="absolute top-full left-0 w-56 z-50 pt-1">
                    <div class="bg-white rounded-xl shadow-xl border border-gray-100 py-2">
                        @foreach([
                            'retail'        => ['site.industry.retail',        'Retail'],
                            'medical'       => ['site.industry.medical',        'Medical'],
                            'government'    => ['site.industry.government',     'Government'],
                            'manufacturing' => ['site.industry.manufacturing',  'Manufacturing'],
                            'logistics'     => ['site.industry.logistics',      'Warehousing & Logistics'],
                            'transport'     => ['site.industry.transport',      'Traffic & Self-service'],
                        ] as $slug => [$key, $default])
                            <a href="{{ route('products.index', ['industry' => $slug]) }}"
                               class="block px-5 py-2 text-sm text-gray-700 hover:text-brand hover:bg-gray-50 transition">
                                {{ __($key) !== $key ? __($key) : $default }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <a href="{{ route('news.index') }}"
               class="px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('news.*') ? 'text-brand' : 'text-gray-700 hover:text-brand' }} transition">
                {{ __('site.nav.news') }}
            </a>

            <a href="{{ route('about') }}"
               class="px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('about') ? 'text-brand' : 'text-gray-700 hover:text-brand' }} transition">
                {{ __('site.nav.about') }}
            </a>

            <a href="{{ route('contact') }}"
               class="px-4 py-2 text-sm font-medium rounded-md {{ request()->routeIs('contact') ? 'text-brand' : 'text-gray-700 hover:text-brand' }} transition">
                {{ __('site.nav.contact') }}
            </a>
        </nav>

        {{-- Right side: WHERE TO BUY + mobile toggle --}}
        <div class="flex items-center gap-3">
            <a href="{{ route('resellers') }}"
               class="hidden sm:inline-flex items-center gap-1.5 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"></path></svg>
                {{ __('site.nav.where_to_buy') }}
            </a>

            {{-- Mobile hamburger --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="lg:hidden p-2 rounded-md text-gray-700 hover:text-brand hover:bg-gray-100 transition"
                    :aria-expanded="mobileOpen">
                <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="lg:hidden border-t border-gray-100 bg-white"
         @click.away="mobileOpen = false">
        <div class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-1" x-data="{ catOpen: false }">
            <a href="{{ route('home') }}" @click="mobileOpen=false"
               class="px-4 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand transition">
                {{ __('site.nav.home') }}
            </a>

            <div>
                <button @click="catOpen = !catOpen"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand transition">
                    {{ __('site.nav.products') }}
                    <svg class="w-4 h-4 transition" :class="catOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="catOpen" class="pl-4 mt-1 flex flex-col gap-1">
                    <a href="{{ route('products.index') }}" @click="mobileOpen=false"
                       class="px-4 py-2 text-sm font-semibold text-brand hover:bg-orange-50 rounded-lg transition">
                        All Products
                    </a>
                    @foreach($categories ?? [] as $category)
                        <a href="{{ route('products.category', $category->slug) }}" @click="mobileOpen=false"
                           class="px-4 py-2 text-sm text-gray-600 hover:text-brand hover:bg-gray-50 rounded-lg transition">
                            {{ $category->getTranslation('name', app()->getLocale()) }}
                        </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('resellers') }}" @click="mobileOpen=false"
               class="px-4 py-3 rounded-lg text-sm font-medium text-brand bg-orange-50 hover:bg-orange-100 transition">
                {{ __('site.nav.where_to_buy') }}
            </a>

            {{-- Industries mobile --}}
            <div x-data="{ indOpen: false }">
                <button @click="indOpen = !indOpen"
                        class="w-full flex items-center justify-between px-4 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand transition">
                    {{ __('site.nav.industries') ?? 'Industries' }}
                    <svg class="w-4 h-4 transition" :class="indOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="indOpen" class="pl-4 mt-1 flex flex-col gap-1">
                    @foreach([
                        'retail'        => 'Retail',
                        'medical'       => 'Medical',
                        'government'    => 'Government',
                        'manufacturing' => 'Manufacturing',
                        'logistics'     => 'Warehousing & Logistics',
                        'transport'     => 'Traffic & Self-service',
                    ] as $slug => $label)
                        <a href="{{ route('products.index', ['industry' => $slug]) }}" @click="mobileOpen=false"
                           class="px-4 py-2 text-sm text-gray-600 hover:text-brand hover:bg-gray-50 rounded-lg transition">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('news.index') }}" @click="mobileOpen=false"
               class="px-4 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand transition">
                {{ __('site.nav.news') }}
            </a>

            <a href="{{ route('about') }}" @click="mobileOpen=false"
               class="px-4 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand transition">
                {{ __('site.nav.about') }}
            </a>

            <a href="{{ route('contact') }}" @click="mobileOpen=false"
               class="px-4 py-3 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-brand transition">
                {{ __('site.nav.contact') }}
            </a>
        </div>
    </div>
</header>

{{-- ─── MAIN CONTENT ─────────────────────────────────────────────────────── --}}
<main class="min-h-screen">
    @yield('content')
</main>

{{-- ─── FOOTER ───────────────────────────────────────────────────────────── --}}
<footer class="bg-neutral-900 text-gray-300 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">

            {{-- Brand column --}}
            <div>
                <span class="text-2xl font-extrabold text-brand">HENEX</span>
                <p class="mt-3 text-sm leading-relaxed">{{ __('site.official_distributor') }}<br>Uzbekistan</p>
                <p class="mt-4 text-xs text-gray-500">HENEX (Guangzhou) Technology Co., Ltd</p>
                <div class="mt-5 flex items-center gap-4">
                    <a href="#" aria-label="Telegram" class="hover:text-white transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    </a>
                    <a href="#" aria-label="WhatsApp" class="hover:text-white transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Products column --}}
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">{{ __('site.nav.products') }}</h4>
                <ul class="space-y-2 text-sm">
                    @foreach($categories ?? [] as $category)
                        <li>
                            <a href="{{ route('products.category', $category->slug) }}"
                               class="hover:text-white transition">
                                {{ $category->getTranslation('name', app()->getLocale()) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Regions column --}}
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">{{ __('site.regions') ?? 'Regions' }}</h4>
                <ul class="space-y-2 text-sm">
                    @foreach($regions ?? [] as $region)
                        <li>
                            <a href="{{ route('resellers') }}?region={{ $region->slug }}" class="hover:text-white transition">
                                {{ $region->getTranslation('name', app()->getLocale()) }}
                            </a>
                        </li>
                    @endforeach
                    <li>
                        <a href="{{ route('resellers') }}" class="text-brand hover:text-brand-light transition font-medium">
                            {{ __('site.nav.where_to_buy') }} →
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Contact column --}}
            <div>
                <h4 class="text-white font-semibold mb-4 text-sm uppercase tracking-wider">{{ __('site.nav.contact') }}</h4>
                <ul class="space-y-3 text-sm">
                    <li>
                        <a href="tel:+998901234567" class="flex items-center gap-2 hover:text-white transition">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"></path></svg>
                            +998 90 123 45 67
                        </a>
                    </li>
                    <li>
                        <a href="mailto:info@henex.uz" class="flex items-center gap-2 hover:text-white transition">
                            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"></path></svg>
                            info@henex.uz
                        </a>
                    </li>
                    <li class="pt-2">
                        <a href="{{ route('contact') }}"
                           class="inline-block bg-brand hover:bg-brand-dark text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                            {{ __('site.nav.contact') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <p>&copy; {{ date('Y') }} HENEX Uzbekistan. {{ __('site.all_rights_reserved') ?? 'All rights reserved.' }}</p>
            <nav class="flex items-center gap-6">
                <a href="{{ route('about') }}" class="hover:text-white transition">{{ __('site.nav.about') }}</a>
                <a href="{{ route('contact') }}" class="hover:text-white transition">{{ __('site.nav.contact') }}</a>
                <a href="{{ route('resellers') }}" class="hover:text-white transition">{{ __('site.nav.where_to_buy') }}</a>
            </nav>
        </div>
    </div>
</footer>

{{-- Telegram floating button --}}
@if($telegramUrl = settings('social_telegram'))
<a href="{{ $telegramUrl }}" target="_blank" rel="noopener"
   class="fixed bottom-6 right-6 z-50 bg-[#26A5E4] hover:bg-[#1a94d4] text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition hover:scale-110"
   aria-label="Telegram">
    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
</a>
@endif

@livewireScripts
</body>
</html>
