@extends('layouts.app')

@section('title', __('site.nav.news') . ' | Henex Uzbekistan')

@section('meta')
    <meta name="description" content="{{ __('site.news.meta_description') }}">
@endsection

@section('content')

{{-- Page Hero --}}
<section class="bg-neutral-950 text-white py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            [__('site.nav.home'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('home'))],
            [__('site.nav.news'), null],
        ]" dark />

        <h1 class="text-3xl sm:text-4xl font-extrabold mt-4 mb-2">
            {{ __('site.news.title') }}
        </h1>
        <p class="text-gray-400 text-lg">{{ __('site.news.subtitle') }}</p>
    </div>
</section>

{{-- Filter Tabs --}}
<div class="bg-white border-b border-gray-200 sticky top-16 z-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex gap-1 overflow-x-auto py-1 scrollbar-hide">
            @foreach([
                '' => __('site.news.type.all'),
                'news' => __('site.news.type.news'),
                'blog' => __('site.news.type.blog'),
                'guide' => __('site.news.type.guide'),
            ] as $value => $label)
            <a href="{{ request()->fullUrlWithQuery(['type' => $value ?: null]) }}"
               class="whitespace-nowrap px-4 py-3 text-sm font-semibold border-b-2 transition
                      {{ (request('type', '') === $value)
                         ? 'border-brand text-brand'
                         : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Articles Grid --}}
<section class="py-12 bg-gray-50 min-h-[60vh]">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        @if($articles->isEmpty())
        <div class="text-center py-24">
            <div class="text-5xl mb-4">📰</div>
            <p class="text-gray-500 text-lg">{{ __('site.news.empty') }}</p>
        </div>
        @else

        {{-- Featured (first article large) --}}
        @if($articles->currentPage() === 1 && $articles->isNotEmpty())
        @php $featured = $articles->first(); @endphp
        <div class="mb-10">
            <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('news.show', $featured->slug)) }}"
               class="group grid grid-cols-1 lg:grid-cols-2 gap-0 bg-white rounded-2xl overflow-hidden shadow-sm border border-gray-200 hover:shadow-lg transition-shadow">
                {{-- Image --}}
                <div class="aspect-video lg:aspect-auto lg:min-h-[320px] overflow-hidden bg-gray-100">
                    @if($featured->cover_image)
                    <img src="{{ Storage::url($featured->cover_image) }}"
                         alt="{{ $featured->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-brand/20 to-brand/5">
                        <svg class="w-20 h-20 text-brand/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                {{-- Content --}}
                <div class="p-8 lg:p-10 flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-brand/10 text-brand text-xs font-bold rounded-full px-3 py-1 uppercase tracking-wide">
                            {{ __('site.news.type.' . $featured->type) }}
                        </span>
                        <span class="text-gray-400 text-sm">
                            {{ $featured->published_at?->format('d M Y') }}
                        </span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-neutral-950 mb-4 group-hover:text-brand transition-colors leading-tight">
                        {{ $featured->title }}
                    </h2>
                    <p class="text-gray-600 text-base leading-relaxed line-clamp-3 mb-6">
                        {{ $featured->excerpt }}
                    </p>
                    <span class="inline-flex items-center text-brand font-bold text-sm gap-1">
                        {{ __('site.news.read_more') }}
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </span>
                </div>
            </a>
        </div>
        @endif

        {{-- Rest of articles --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles->skip($articles->currentPage() === 1 ? 1 : 0) as $article)
            <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('news.show', $article->slug)) }}"
               class="group bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-lg transition-shadow flex flex-col">
                {{-- Cover --}}
                <div class="aspect-video overflow-hidden bg-gray-100">
                    @if($article->cover_image)
                    <img src="{{ Storage::url($article->cover_image) }}"
                         alt="{{ $article->title }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-50">
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="bg-brand/10 text-brand text-xs font-bold rounded-full px-2.5 py-0.5 uppercase">
                            {{ __('site.news.type.' . $article->type) }}
                        </span>
                        <span class="text-gray-400 text-xs">{{ $article->published_at?->format('d M Y') }}</span>
                    </div>
                    <h3 class="font-bold text-neutral-950 text-lg leading-snug mb-2 group-hover:text-brand transition-colors line-clamp-2">
                        {{ $article->title }}
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 flex-1">
                        {{ $article->excerpt }}
                    </p>
                    <div class="mt-4 flex items-center text-brand text-sm font-semibold gap-1">
                        {{ __('site.news.read_more') }}
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($articles->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $articles->withQueryString()->links() }}
        </div>
        @endif

        @endif
    </div>
</section>

@endsection
