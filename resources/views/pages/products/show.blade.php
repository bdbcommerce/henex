@extends('layouts.app')

@section('title', $product->getTranslation('meta_title', app()->getLocale()) ?: $product->name . ' | Henex Uzbekistan')

@section('meta')
    <meta name="description" content="{{ $product->getTranslation('meta_description', app()->getLocale()) ?: $product->short_description }}">
    <meta property="og:title" content="{{ $product->name }} | Henex Uzbekistan">
    <meta property="og:description" content="{{ $product->short_description }}">
    <meta property="og:image" content="{{ $product->getFirstMediaUrl('gallery') }}">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:type" content="product">

    {{-- Hreflang --}}
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
        <link rel="alternate" hreflang="{{ $localeCode }}"
              href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" />
    @endforeach
    <link rel="alternate" hreflang="x-default"
          href="{{ LaravelLocalization::getLocalizedURL('uz', null, [], true) }}" />
@endsection

@section('schema')
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "{{ addslashes($product->name) }}",
    "image": [
        @foreach($product->getMedia('gallery') as $media)
            "{{ $media->getUrl() }}"{{ !$loop->last ? ',' : '' }}
        @endforeach
    ],
    "description": "{{ addslashes(strip_tags($product->short_description)) }}",
    "sku": "{{ $product->sku }}",
    "brand": {
        "@type": "Brand",
        "name": "HENEX"
    },
    "offers": {
        "@type": "Offer",
        "availability": "https://schema.org/InStoreOnly",
        "seller": {
            "@type": "Organization",
            "name": "Henex Uzbekistan"
        }
    }
}
</script>
@endsection

@section('content')

{{-- Breadcrumb --}}
<div class="bg-gray-50 border-b border-gray-200 py-3">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            [__('site.nav.home'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('home'))],
            [__('site.nav.products'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('products.index'))],
            [$product->categories->first()?->name, $product->categories->first() ? LaravelLocalization::getLocalizedURL(app()->getLocale(), route('products.index', ['category' => $product->categories->first()->slug])) : null],
            [$product->name, null],
        ]" />
    </div>
</div>

{{-- Main Product Section --}}
<section class="py-10 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 xl:gap-16">

            {{-- Left: Image Gallery --}}
            <div x-data="{ activeImg: '{{ $product->getFirstMediaUrl('gallery') }}' }">

                {{-- Main Image --}}
                <div class="bg-gray-50 rounded-2xl overflow-hidden aspect-square flex items-center justify-center p-6 mb-4 border border-gray-100">
                    <img :src="activeImg"
                         alt="{{ $product->name }}"
                         class="max-h-full max-w-full object-contain transition-opacity duration-300">
                </div>

                {{-- Thumbnails --}}
                @if($product->getMedia('gallery')->count() > 1)
                <div class="grid grid-cols-5 gap-2">
                    @foreach($product->getMedia('gallery') as $media)
                    <button type="button"
                            @click="activeImg = '{{ $media->getUrl() }}'"
                            :class="activeImg === '{{ $media->getUrl() }}' ? 'ring-2 ring-brand' : 'ring-1 ring-gray-200'"
                            class="rounded-lg overflow-hidden bg-gray-50 aspect-square flex items-center justify-center p-2 hover:ring-brand transition-all">
                        <img src="{{ $media->getUrl('thumb') ?: $media->getUrl() }}"
                             alt="{{ $product->name }}"
                             class="max-h-full max-w-full object-contain">
                    </button>
                    @endforeach
                </div>
                @endif

                {{-- Documents --}}
                @if($product->getMedia('documents')->count())
                <div class="mt-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 uppercase tracking-wide">
                        {{ __('site.product.documents') }}
                    </h3>
                    <div class="flex flex-col gap-2">
                        @foreach($product->getMedia('documents') as $doc)
                        <a href="{{ $doc->getUrl() }}" target="_blank" rel="noopener"
                           class="flex items-center gap-3 text-sm text-brand hover:underline font-medium">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            {{ $doc->name ?: __('site.product.datasheet') }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            {{-- Right: Info + CTA --}}
            <div class="flex flex-col">

                {{-- Category badge --}}
                @if($product->categories->isNotEmpty())
                <div class="mb-3">
                    @foreach($product->categories as $cat)
                    <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('products.index', ['category' => $cat->slug])) }}"
                       class="inline-block bg-brand/10 text-brand text-xs font-semibold rounded-full px-3 py-1 mr-1 hover:bg-brand/20 transition">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </div>
                @endif

                {{-- Name + SKU --}}
                <h1 class="text-3xl sm:text-4xl font-extrabold text-neutral-950 leading-tight mb-2">
                    {{ $product->name }}
                </h1>
                @if($product->sku)
                <p class="text-sm text-gray-500 mb-4">SKU: <span class="font-mono font-medium">{{ $product->sku }}</span></p>
                @endif

                {{-- Short description --}}
                @if($product->short_description)
                <p class="text-gray-600 text-lg leading-relaxed mb-6">
                    {{ $product->short_description }}
                </p>
                @endif

                {{-- Trust badges --}}
                <div class="flex flex-wrap gap-3 mb-8">
                    <div class="flex items-center gap-2 bg-green-50 text-green-700 rounded-lg px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        {{ __('site.trust.warranty') }}
                    </div>
                    <div class="flex items-center gap-2 bg-blue-50 text-blue-700 rounded-lg px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('site.trust.official') }}
                    </div>
                    <div class="flex items-center gap-2 bg-orange-50 text-brand rounded-lg px-3 py-2 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        {{ __('site.trust.fast_delivery') }}
                    </div>
                </div>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    {{-- Quote modal trigger --}}
                    <button type="button"
                            @click="$dispatch('open-quote-modal', { productId: {{ $product->id }}, productName: '{{ addslashes($product->name) }}' })"
                            class="flex-1 bg-brand hover:bg-brand-dark text-white font-bold py-4 px-6 rounded-xl transition text-center text-lg">
                        {{ __('site.cta.request_quote') }}
                    </button>
                    {{-- Where to buy --}}
                    <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('resellers')) }}"
                       class="flex-1 border-2 border-neutral-950 hover:bg-neutral-950 hover:text-white text-neutral-950 font-bold py-4 px-6 rounded-xl transition text-center text-lg">
                        {{ __('site.cta.find_reseller') }}
                    </a>
                </div>

                {{-- Quick specs preview (first 4 specs) --}}
                @if($product->specifications->isNotEmpty())
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <table class="w-full text-sm">
                        @foreach($product->specifications->take(4) as $spec)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="py-3 px-4 font-semibold text-gray-700 w-2/5 border-b border-gray-100">
                                {{ $spec->getTranslation('key', app()->getLocale()) }}
                            </td>
                            <td class="py-3 px-4 text-gray-600 border-b border-gray-100">
                                {{ $spec->getTranslation('value', app()->getLocale()) }}
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    @if($product->specifications->count() > 4)
                    <div class="py-2 px-4 bg-gray-50 text-center">
                        <a href="#specifications" class="text-brand text-sm font-semibold hover:underline">
                            {{ __('site.product.view_all_specs') }} ↓
                        </a>
                    </div>
                    @endif
                </div>
                @endif

            </div>
        </div>
    </div>
</section>

{{-- Full Description --}}
@if($product->description)
<section class="py-12 bg-gray-50 border-t border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-neutral-950 mb-6">{{ __('site.product.description') }}</h2>
            <div class="prose prose-lg max-w-none text-gray-700">
                {!! $product->description !!}
            </div>
        </div>
    </div>
</section>
@endif

{{-- Full Specifications Table --}}
@if($product->specifications->isNotEmpty())
<section id="specifications" class="py-12 bg-white border-t border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-neutral-950 mb-6">{{ __('site.product.specifications') }}</h2>
            <div class="border border-gray-200 rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <tbody>
                        @foreach($product->specifications as $spec)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                            <td class="py-3.5 px-5 font-semibold text-gray-700 w-2/5 border-b border-gray-100">
                                {{ $spec->getTranslation('key', app()->getLocale()) }}
                            </td>
                            <td class="py-3.5 px-5 text-gray-600 border-b border-gray-100">
                                {{ $spec->getTranslation('value', app()->getLocale()) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Related Products --}}
@if($relatedProducts->isNotEmpty())
<section class="py-12 bg-gray-50 border-t border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-neutral-950 mb-8">{{ __('site.product.related') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($relatedProducts as $related)
                <x-site.product-card :product="$related" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Quote Form Modal --}}
@livewire('frontend.product-quote-form')

{{-- CTA Strip --}}
<section class="bg-neutral-950 text-white py-14">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-2xl sm:text-3xl font-extrabold mb-3">{{ __('site.cta.need_help') }}</h2>
        <p class="text-gray-400 mb-8 max-w-xl mx-auto">{{ __('site.cta.help_subtitle') }}</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="tel:{{ settings('site_phone') }}"
               class="bg-brand hover:bg-brand-dark text-white font-bold py-4 px-8 rounded-xl transition text-lg">
                {{ settings('site_phone') }}
            </a>
            <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('contact')) }}"
               class="border-2 border-white hover:bg-white hover:text-neutral-950 text-white font-bold py-4 px-8 rounded-xl transition text-lg">
                {{ __('site.nav.contact') }}
            </a>
        </div>
    </div>
</section>

@endsection
