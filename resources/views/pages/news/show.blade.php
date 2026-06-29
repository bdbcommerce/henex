@extends('layouts.app')

@section('title', $article->getTranslation('meta_title', app()->getLocale()) ?: $article->title . ' | Henex Uzbekistan')

@section('meta')
    <meta name="description" content="{{ $article->getTranslation('meta_description', app()->getLocale()) ?: $article->excerpt }}">
    <meta property="og:title" content="{{ $article->title }}">
    <meta property="og:description" content="{{ $article->excerpt }}">
    @if($article->cover_image)
    <meta property="og:image" content="{{ Storage::url($article->cover_image) }}">
    @endif
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="{{ $article->published_at?->toIso8601String() }}">

    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        <link rel="alternate" hreflang="{{ $localeCode }}"
              href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" />
    @endforeach
@endsection

@section('content')

{{-- Hero / Cover --}}
@if($article->cover_image)
<div class="relative h-64 sm:h-80 lg:h-[420px] overflow-hidden bg-neutral-950">
    <img src="{{ Storage::url($article->cover_image) }}"
         alt="{{ $article->title }}"
         class="w-full h-full object-cover opacity-60">
    <div class="absolute inset-0 bg-gradient-to-t from-neutral-950/80 to-transparent"></div>
    <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-10">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <x-site.breadcrumb :items="[
                [__('site.nav.home'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('home'))],
                [__('site.nav.news'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('news.index'))],
                [$article->title, null],
            ]" dark />
            <h1 class="text-2xl sm:text-4xl font-extrabold text-white mt-3 leading-tight max-w-3xl">
                {{ $article->title }}
            </h1>
        </div>
    </div>
</div>
@else
<section class="bg-neutral-950 text-white py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            [__('site.nav.home'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('home'))],
            [__('site.nav.news'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('news.index'))],
            [$article->title, null],
        ]" dark />
        <h1 class="text-2xl sm:text-4xl font-extrabold mt-4 leading-tight max-w-3xl">
            {{ $article->title }}
        </h1>
    </div>
</section>
@endif

{{-- Article Body --}}
<section class="py-12 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            {{-- Main content --}}
            <div class="lg:col-span-2">

                {{-- Meta bar --}}
                <div class="flex flex-wrap items-center gap-4 mb-8 pb-6 border-b border-gray-200">
                    <span class="bg-brand/10 text-brand text-xs font-bold rounded-full px-3 py-1.5 uppercase tracking-wide">
                        {{ __('site.news.type.' . $article->type) }}
                    </span>
                    <span class="flex items-center gap-1.5 text-gray-500 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $article->published_at?->format('d M Y') }}
                    </span>
                    @if($article->author)
                    <span class="flex items-center gap-1.5 text-gray-500 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        {{ $article->author->name }}
                    </span>
                    @endif
                </div>

                {{-- Excerpt --}}
                @if($article->excerpt)
                <p class="text-xl text-gray-600 leading-relaxed mb-8 font-medium">
                    {{ $article->excerpt }}
                </p>
                @endif

                {{-- Full Content --}}
                <div class="prose prose-lg max-w-none text-gray-700
                            prose-headings:font-extrabold prose-headings:text-neutral-950
                            prose-a:text-brand prose-a:no-underline hover:prose-a:underline
                            prose-img:rounded-xl prose-img:shadow-md">
                    {!! $article->content !!}
                </div>

                {{-- Share --}}
                <div class="mt-10 pt-8 border-t border-gray-200">
                    <p class="text-sm font-semibold text-gray-500 mb-3">{{ __('site.news.share') }}</p>
                    <div class="flex gap-3">
                        <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($article->title) }}"
                           target="_blank" rel="noopener"
                           class="flex items-center gap-2 bg-[#229ED9] text-white text-sm font-semibold px-4 py-2 rounded-lg hover:opacity-90 transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                            Telegram
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($article->title . ' ' . request()->url()) }}"
                           target="_blank" rel="noopener"
                           class="flex items-center gap-2 bg-[#25D366] text-white text-sm font-semibold px-4 py-2 rounded-lg hover:opacity-90 transition">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                            </svg>
                            WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">

                {{-- CTA Card --}}
                <div class="bg-neutral-950 text-white rounded-2xl p-6 mb-8">
                    <h3 class="font-bold text-lg mb-2">{{ __('site.sidebar.cta_title') }}</h3>
                    <p class="text-gray-400 text-sm mb-5">{{ __('site.sidebar.cta_subtitle') }}</p>
                    <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('contact')) }}"
                       class="block text-center bg-brand hover:bg-brand-dark text-white font-bold py-3 px-5 rounded-xl transition text-sm">
                        {{ __('site.cta.request_quote') }}
                    </a>
                    <a href="tel:{{ settings('site_phone') }}"
                       class="block text-center text-gray-400 hover:text-white text-sm mt-3 transition">
                        {{ settings('site_phone') }}
                    </a>
                </div>

                {{-- Recent articles --}}
                @if($recentArticles->isNotEmpty())
                <div>
                    <h3 class="font-bold text-neutral-950 text-base mb-4 uppercase tracking-wide">
                        {{ __('site.news.recent') }}
                    </h3>
                    <div class="flex flex-col gap-4">
                        @foreach($recentArticles as $recent)
                        <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('news.show', $recent->slug)) }}"
                           class="group flex gap-3 items-start">
                            <div class="w-16 h-14 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                @if($recent->cover_image)
                                <img src="{{ Storage::url($recent->cover_image) }}"
                                     alt="{{ $recent->title }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                @else
                                <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                                    </svg>
                                </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-400 mb-1">{{ $recent->published_at?->format('d M Y') }}</p>
                                <h4 class="text-sm font-semibold text-neutral-950 group-hover:text-brand transition-colors line-clamp-2 leading-snug">
                                    {{ $recent->title }}
                                </h4>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Product categories --}}
                <div class="mt-8 bg-gray-50 rounded-2xl p-5">
                    <h3 class="font-bold text-neutral-950 text-base mb-4">{{ __('site.nav.products') }}</h3>
                    <div class="flex flex-col gap-1">
                        @foreach($categories ?? [] as $category)
                        <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('products.index', ['category' => $category->slug])) }}"
                           class="text-sm text-gray-600 hover:text-brand hover:font-medium transition py-1 border-b border-gray-200 last:border-0">
                            {{ $category->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
