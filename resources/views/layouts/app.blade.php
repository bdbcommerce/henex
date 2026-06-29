<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <!-- SEO Meta Tags -->
    @include('partials.seo-meta', [
        'meta_title' => $meta_title ?? __('site.official_distributor'),
        'meta_description' => $meta_description ?? 'Official distributor of HENEX barcode scanners in Uzbekistan.',
        'meta_image' => $meta_image ?? asset('images/henex-og.jpg'),
        'meta_url' => $meta_url ?? url()->current(),
        'meta_type' => $meta_type ?? 'website'
    ])

    <title>@yield('title', 'HENEX Uzbekistan - ' . __('site.official_distributor'))</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-white text-neutral-950 font-sans">
    <!-- Sticky Header -->
    <header class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-2">
                <h1 class="text-2xl font-bold text-brand">HENEX</h1>
                <span class="text-sm text-gray-600">{{ __('site.official_distributor') }}</span>
            </div>

            <!-- Nav -->
            <nav class="hidden md:flex space-x-8">
                <a href="{{ route('home') }}" class="text-sm font-medium hover:text-brand transition">{{ __('site.nav.home') }}</a>
                <a href="{{ route('products.index') }}" class="text-sm font-medium hover:text-brand transition">{{ __('site.nav.products') }}</a>
                <a href="{{ route('resellers') }}" class="text-sm font-medium hover:text-brand transition">{{ __('site.nav.where_to_buy') }}</a>
                <a href="{{ route('news.index') }}" class="text-sm font-medium hover:text-brand transition">{{ __('site.nav.news') }}</a>
                <a href="{{ route('about') }}" class="text-sm font-medium hover:text-brand transition">{{ __('site.nav.about') }}</a>
            </nav>

            <!-- Locale Switcher + Phone CTA -->
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    @foreach(['uz' => 'Oʻz', 'ru' => 'Рус', 'en' => 'Eng'] as $code => $label)
                        <form action="{{ route('locale.set', $code) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="text-xs font-semibold {{ app()->getLocale() === $code ? 'text-brand' : 'text-gray-500 hover:text-brand' }} transition bg-transparent border-0 p-0 cursor-pointer">
                                {{ $label }}
                            </button>
                        </form>
                        @if(!$loop->last)<span class="text-gray-300">|</span>@endif
                    @endforeach
                </div>
                <a href="tel:+99890123456" class="text-sm font-semibold text-brand hover:text-brand-dark transition">
                    +998 90 123 45 67
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-neutral-950 text-white mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <!-- Brand -->
                <div>
                    <h3 class="text-lg font-bold text-brand mb-4">HENEX</h3>
                    <p class="text-gray-400 text-sm">{{ __('site.official_distributor') }}</p>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="font-semibold mb-4">{{ __('site.nav.products') }}</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition">{{ __('site.nav.products') }}</a></li>
                        <li><a href="{{ route('resellers') }}" class="hover:text-white transition">{{ __('site.nav.where_to_buy') }}</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">{{ __('site.nav.contact') }}</a></li>
                    </ul>
                </div>

                <!-- Regions -->
                <div>
                    <h4 class="font-semibold mb-4">{{ __('site.regions') }}</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        @foreach($regions ?? [] as $region)
                            <li><a href="{{ route('resellers') }}?region={{ $region->slug }}" class="hover:text-white transition">{{ $region->getTranslation('name', app()->getLocale()) }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h4 class="font-semibold mb-4">{{ __('site.contact') }}</h4>
                    <p class="text-gray-400 text-sm mb-2">📞 +998 90 123 45 67</p>
                    <p class="text-gray-400 text-sm mb-4">📧 info@henex.uz</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition">Telegram</a>
                        <a href="#" class="text-gray-400 hover:text-white transition">WhatsApp</a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} HENEX Uzbekistan. {{ __('site.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
