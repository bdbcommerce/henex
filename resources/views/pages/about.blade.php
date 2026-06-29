@extends('layouts.app')

@section('title', __('site.nav.about') . ' | Henex Uzbekistan')

@section('meta')
    <meta name="description" content="{{ __('site.about.meta_description') }}">
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Henex Uzbekistan",
    "url": "{{ url('/') }}",
    "logo": "{{ asset('images/logo.png') }}",
    "description": "{{ __('site.about.meta_description') }}",
    "address": {
        "@type": "PostalAddress",
        "addressCountry": "UZ",
        "addressLocality": "Tashkent"
    },
    "telephone": "{{ settings('site_phone') }}",
    "email": "{{ settings('site_email') }}",
    "sameAs": [
        "{{ settings('social_telegram') }}",
        "{{ settings('social_instagram') }}",
        "{{ settings('social_youtube') }}"
    ]
}
</script>
@endsection

@section('content')

{{-- Hero --}}
<section class="bg-neutral-950 text-white py-16">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            [__('site.nav.home'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('home'))],
            [__('site.nav.about'), null],
        ]" dark />
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mt-6">
            <div>
                <div class="inline-block bg-brand/20 text-brand text-sm font-semibold rounded-full px-4 py-1.5 mb-5">
                    {{ __('site.about.badge') }}
                </div>
                <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-5">
                    {{ __('site.about.hero_title') }}
                </h1>
                <p class="text-gray-400 text-lg leading-relaxed">
                    {{ __('site.about.hero_subtitle') }}
                </p>
            </div>
            <div class="hidden lg:flex justify-end">
                <div class="grid grid-cols-2 gap-4 w-full max-w-sm">
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                        <div class="text-4xl font-extrabold text-brand mb-1">2020</div>
                        <div class="text-gray-400 text-sm">{{ __('site.about.stat_founded') }}</div>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                        <div class="text-4xl font-extrabold text-brand mb-1">67+</div>
                        <div class="text-gray-400 text-sm">{{ __('site.about.stat_products') }}</div>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                        <div class="text-4xl font-extrabold text-brand mb-1">14</div>
                        <div class="text-gray-400 text-sm">{{ __('site.about.stat_regions') }}</div>
                    </div>
                    <div class="bg-white/5 border border-white/10 rounded-2xl p-6 text-center">
                        <div class="text-4xl font-extrabold text-brand mb-1">1000+</div>
                        <div class="text-gray-400 text-sm">{{ __('site.about.stat_clients') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Who We Are --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
            <div>
                <h2 class="text-3xl font-extrabold text-neutral-950 mb-5">{{ __('site.about.who_title') }}</h2>
                <div class="prose prose-lg text-gray-600 max-w-none">
                    <p>{{ __('site.about.who_p1') }}</p>
                    <p>{{ __('site.about.who_p2') }}</p>
                    <p>{{ __('site.about.who_p3') }}</p>
                </div>
            </div>
            <div class="bg-gray-50 rounded-2xl p-8">
                <img src="{{ asset('images/about-henex.jpg') }}"
                     alt="Henex Uzbekistan"
                     onerror="this.style.display='none'"
                     class="rounded-xl w-full object-cover">
                {{-- Fallback placeholder --}}
                <div class="text-center py-12 text-gray-300">
                    <svg class="w-20 h-20 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="text-sm">Henex Uzbekistan Office</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Why Henex - 4 Pillars --}}
<section class="py-16 bg-gray-50 border-t border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-neutral-950 mb-3">{{ __('site.about.why_title') }}</h2>
            <p class="text-gray-500 text-lg max-w-2xl mx-auto">{{ __('site.about.why_subtitle') }}</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'brand', 'key' => 'official'],
                ['icon' => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z', 'color' => 'blue-600', 'key' => 'warranty'],
                ['icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'green-600', 'key' => 'support'],
                ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'purple-600', 'key' => 'delivery'],
            ] as $pillar)
            <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-200 text-center">
                <div class="w-14 h-14 bg-{{ $pillar['color'] }}/10 rounded-2xl flex items-center justify-center mx-auto mb-5">
                    <svg class="w-7 h-7 text-{{ $pillar['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $pillar['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="font-bold text-neutral-950 text-lg mb-2">{{ __('site.why.' . $pillar['key'] . '_title') }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ __('site.why.' . $pillar['key'] . '_desc') }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- About HENEX China --}}
<section class="py-16 bg-white border-t border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-3xl font-extrabold text-neutral-950 mb-5">{{ __('site.about.henex_title') }}</h2>
            <p class="text-gray-600 text-lg leading-relaxed mb-6">{{ __('site.about.henex_p1') }}</p>
            <p class="text-gray-600 leading-relaxed mb-8">{{ __('site.about.henex_p2') }}</p>
            <a href="https://www.henex.cn" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 text-brand font-bold hover:underline">
                www.henex.cn
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
            </a>
        </div>
    </div>
</section>

{{-- Industry Applications --}}
<section class="py-16 bg-gray-50 border-t border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-extrabold text-neutral-950 mb-3">{{ __('site.about.industries_title') }}</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach([
                ['emoji' => '🛒', 'key' => 'retail'],
                ['emoji' => '🏭', 'key' => 'manufacturing'],
                ['emoji' => '📦', 'key' => 'warehousing'],
                ['emoji' => '🏥', 'key' => 'medical'],
                ['emoji' => '🚚', 'key' => 'logistics'],
                ['emoji' => '🏪', 'key' => 'pos'],
            ] as $industry)
            <div class="bg-white rounded-2xl p-5 text-center shadow-sm border border-gray-200">
                <div class="text-4xl mb-3">{{ $industry['emoji'] }}</div>
                <div class="text-sm font-semibold text-neutral-950">{{ __('site.industry.' . $industry['key']) }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="py-16 bg-neutral-950 text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-extrabold mb-3">{{ __('site.about.cta_title') }}</h2>
        <p class="text-gray-400 text-lg mb-8 max-w-xl mx-auto">{{ __('site.about.cta_subtitle') }}</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('products.index')) }}"
               class="bg-brand hover:bg-brand-dark text-white font-bold py-4 px-8 rounded-xl transition text-lg">
                {{ __('site.about.cta_products') }}
            </a>
            <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('contact')) }}"
               class="border-2 border-white hover:bg-white hover:text-neutral-950 text-white font-bold py-4 px-8 rounded-xl transition text-lg">
                {{ __('site.nav.contact') }}
            </a>
        </div>
    </div>
</section>

@endsection
