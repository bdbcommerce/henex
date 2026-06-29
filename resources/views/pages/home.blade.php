@extends('layouts.app')

@section('title', 'HENEX - Official Barcode Scanner Distributor in Uzbekistan')
@section('description', 'Buy HENEX barcode scanners in Uzbekistan. Official distributor with warranty, technical support, and fast delivery.')

@section('content')
<!-- ===== HERO CAROUSEL ===== -->
<section class="relative h-[500px] md:h-[600px] overflow-hidden bg-black">
    <div class="swiper-container swiper-banner h-full">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide h-full flex items-center justify-center relative">
                <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent z-10"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-brand/5 to-neutral-950/20 z-10"></div>
                <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white w-full">
                    <div class="max-w-2xl">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4">Official HENEX Distributor</h1>
                        <p class="text-lg md:text-xl text-gray-200 mb-8">Trusted barcode scanning solutions for Uzbekistan</p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('products.index') }}" class="bg-brand hover:bg-brand-dark text-white px-8 py-3 rounded-lg font-semibold transition inline-block">View Products</a>
                            <a href="{{ route('resellers') }}" class="bg-white/20 hover:bg-white/30 text-white px-8 py-3 rounded-lg font-semibold transition backdrop-blur-sm inline-block border border-white/40">Find Reseller</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide h-full flex items-center justify-center relative">
                <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent z-10"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-brand/5 to-neutral-950/20 z-10"></div>
                <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white w-full">
                    <div class="max-w-2xl">
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4">Wireless Solutions</h2>
                        <p class="text-lg md:text-xl text-gray-200 mb-8">2D & 1D wireless barcode scanners for your business</p>
                        <a href="{{ route('products.index') }}" class="bg-brand hover:bg-brand-dark text-white px-8 py-3 rounded-lg font-semibold transition inline-block">Explore Wireless</a>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide h-full flex items-center justify-center relative">
                <div class="absolute inset-0 bg-gradient-to-r from-black/50 to-transparent z-10"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-brand/5 to-neutral-950/20 z-10"></div>
                <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-white w-full">
                    <div class="max-w-2xl">
                        <h2 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-4">Industrial Grade</h2>
                        <p class="text-lg md:text-xl text-gray-200 mb-8">Durable scanners for manufacturing & warehousing</p>
                        <a href="{{ route('products.index') }}" class="bg-brand hover:bg-brand-dark text-white px-8 py-3 rounded-lg font-semibold transition inline-block">View Industrial</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="swiper-pagination absolute bottom-6 left-1/2 -translate-x-1/2 z-20"></div>

        <button class="swiper-button-prev absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center transition backdrop-blur-sm hidden md:flex">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button class="swiper-button-next absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 bg-white/20 hover:bg-white/40 rounded-full flex items-center justify-center transition backdrop-blur-sm hidden md:flex">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
    </div>
</section>

<!-- ===== TRUST BADGES ===== -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center group hover:shadow-lg rounded-xl p-6 transition">
                <div class="w-16 h-16 bg-brand/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-brand/20 transition">
                    <svg class="w-8 h-8 text-brand" fill="currentColor" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"></path></svg>
                </div>
                <h3 class="font-bold text-lg text-neutral-950 mb-2">{{ __('site.official_distributor') }}</h3>
                <p class="text-gray-600 text-sm">Authorized by HENEX (Guangzhou) Technology Co., Ltd</p>
            </div>

            <div class="text-center group hover:shadow-lg rounded-xl p-6 transition">
                <div class="w-16 h-16 bg-brand/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-brand/20 transition">
                    <svg class="w-8 h-8 text-brand" fill="currentColor" viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12h-8v2h8v-2zm0-3h-8v2h8V11zm0-3H4V6h14v2z"></path></svg>
                </div>
                <h3 class="font-bold text-lg text-neutral-950 mb-2">1-2 Year Warranty</h3>
                <p class="text-gray-600 text-sm">Official warranty coverage on all products</p>
            </div>

            <div class="text-center group hover:shadow-lg rounded-xl p-6 transition">
                <div class="w-16 h-16 bg-brand/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-brand/20 transition">
                    <svg class="w-8 h-8 text-brand" fill="currentColor" viewBox="0 0 24 24"><path d="M15.5 1h-8C6.12 1 5 2.12 5 3.5v17C5 21.88 6.12 23 7.5 23h8c1.38 0 2.5-1.12 2.5-2.5v-17C18 2.12 16.88 1 15.5 1zm-4 21c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm4.5-4H7V4h9v14z"></path></svg>
                </div>
                <h3 class="font-bold text-lg text-neutral-950 mb-2">24/7 Technical Support</h3>
                <p class="text-gray-600 text-sm">Professional customer support available anytime</p>
            </div>

            <div class="text-center group hover:shadow-lg rounded-xl p-6 transition">
                <div class="w-16 h-16 bg-brand/10 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-brand/20 transition">
                    <svg class="w-8 h-8 text-brand" fill="currentColor" viewBox="0 0 24 24"><path d="M18 18.5a1.5 1.5 0 01-1.5-1.5 1.5 1.5 0 011.5-1.5 1.5 1.5 0 011.5 1.5 1.5 1.5 0 01-1.5 1.5m1.5-9l1.96 2.5H17V9.5m-11 9A1.5 1.5 0 015.5 17 1.5 1.5 0 017 15.5 1.5 1.5 0 015.5 14m0-5H1v6h4.5V9m6.5.5h5v3h-5zm-6-4v2h14V4H3.5m15 9.5H17v2.5h1.5V13.5z"></path></svg>
                </div>
                <h3 class="font-bold text-lg text-neutral-950 mb-2">Fast Delivery</h3>
                <p class="text-gray-600 text-sm">Same-day delivery in Tashkent, nationwide shipping</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURED CATEGORIES ===== -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-neutral-950 mb-4">Product Categories</h2>
            <p class="text-gray-600 text-lg">Choose from our comprehensive range of barcode solutions</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($categories ?? [] as $category)
                <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-xl transition">
                    <div class="aspect-video bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-5xl group-hover:scale-105 transition">📡</div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-neutral-950 group-hover:text-brand transition line-clamp-2">
                            {{ $category->getTranslation('name', app()->getLocale()) }}
                        </h3>
                        <div class="mt-4 flex items-center text-brand font-semibold text-sm">
                            Explore <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </div>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-center text-gray-500">Categories coming soon...</p>
            @endforelse
        </div>
    </div>
</section>

<!-- ===== FEATURED PRODUCTS ===== -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-neutral-950 mb-4">Featured Products</h2>
            <p class="text-gray-600 text-lg">Our most popular and recommended barcode scanners</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($featuredProducts ?? [] as $product)
                <a href="{{ route('products.show', $product->slug) }}" class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-xl transition border border-gray-100">
                    <div class="aspect-square bg-gray-100 flex items-center justify-center text-4xl group-hover:scale-105 transition">📱</div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-neutral-950 group-hover:text-brand transition line-clamp-2">
                            {{ $product->getTranslation('name', app()->getLocale()) }}
                        </h3>
                        @if($product->sku)
                            <p class="text-sm text-gray-600 mt-2">SKU: {{ $product->sku }}</p>
                        @endif
                        <button class="mt-4 w-full py-2 bg-brand text-white rounded-lg text-sm font-semibold hover:bg-brand-dark transition">
                            View Details
                        </button>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-center text-gray-500">Featured products coming soon...</p>
            @endforelse
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('products.index') }}" class="bg-brand hover:bg-brand-dark text-white px-8 py-3 rounded-lg font-semibold transition inline-block">View All Products →</a>
        </div>
    </div>
</section>

<!-- ===== INDUSTRIES ===== -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-neutral-950 mb-4">Industry Solutions</h2>
            <p class="text-gray-600 text-lg">Proven solutions for every sector</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-lg transition group">
                <div class="w-12 h-12 bg-brand/10 rounded-lg flex items-center justify-center mb-4 group-hover:bg-brand/20 transition text-2xl">🏪</div>
                <h3 class="font-bold text-lg text-neutral-950 mb-3">Retail & E-Commerce</h3>
                <p class="text-gray-600">Fast, accurate POS barcode scanning for retail and fulfillment centers</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-lg transition group">
                <div class="w-12 h-12 bg-brand/10 rounded-lg flex items-center justify-center mb-4 group-hover:bg-brand/20 transition text-2xl">🏭</div>
                <h3 class="font-bold text-lg text-neutral-950 mb-3">Manufacturing</h3>
                <p class="text-gray-600">Industrial-grade scanners for production lines and quality control</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-lg transition group">
                <div class="w-12 h-12 bg-brand/10 rounded-lg flex items-center justify-center mb-4 group-hover:bg-brand/20 transition text-2xl">🏥</div>
                <h3 class="font-bold text-lg text-neutral-950 mb-3">Healthcare</h3>
                <p class="text-gray-600">Patient identification and medication tracking solutions</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-lg transition group">
                <div class="w-12 h-12 bg-brand/10 rounded-lg flex items-center justify-center mb-4 group-hover:bg-brand/20 transition text-2xl">📦</div>
                <h3 class="font-bold text-lg text-neutral-950 mb-3">Logistics & Warehouse</h3>
                <p class="text-gray-600">Package sorting, tracking, and inventory management solutions</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-lg transition group">
                <div class="w-12 h-12 bg-brand/10 rounded-lg flex items-center justify-center mb-4 group-hover:bg-brand/20 transition text-2xl">🚌</div>
                <h3 class="font-bold text-lg text-neutral-950 mb-3">Transportation</h3>
                <p class="text-gray-600">Ticket validation and asset tracking for mobility services</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-8 hover:shadow-lg transition group">
                <div class="w-12 h-12 bg-brand/10 rounded-lg flex items-center justify-center mb-4 group-hover:bg-brand/20 transition text-2xl">🏛️</div>
                <h3 class="font-bold text-lg text-neutral-950 mb-3">Government</h3>
                <p class="text-gray-600">Document verification and public service automation</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== LATEST NEWS ===== -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-neutral-950 mb-4">Latest News</h2>
            <p class="text-gray-600 text-lg">Stay updated with HENEX news and industry insights</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @forelse($latestArticles ?? [] as $article)
                <a href="{{ route('news.show', $article->slug) }}" class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition border border-gray-100">
                    <div class="aspect-video bg-gray-100 flex items-center justify-center text-4xl group-hover:scale-105 transition overflow-hidden">📰</div>
                    <div class="p-6">
                        <span class="text-xs text-brand font-semibold uppercase">{{ $article->type }}</span>
                        <h3 class="font-bold text-lg text-neutral-950 mt-3 group-hover:text-brand transition line-clamp-2">
                            {{ $article->getTranslation('title', app()->getLocale()) }}
                        </h3>
                        <p class="text-gray-600 text-sm mt-3 line-clamp-2">
                            {{ $article->getTranslation('excerpt', app()->getLocale()) ?? Str::limit(strip_tags($article->getTranslation('content', app()->getLocale())), 100) }}
                        </p>
                        <time class="text-xs text-gray-500 mt-4 block">
                            {{ $article->published_at?->format('M d, Y') }}
                        </time>
                    </div>
                </a>
            @empty
                <p class="col-span-full text-center text-gray-500">News articles coming soon...</p>
            @endforelse
        </div>

        <div class="text-center mt-12">
            <a href="{{ route('news.index') }}" class="text-brand font-semibold hover:underline">View all news →</a>
        </div>
    </div>
</section>

<!-- ===== CTA SECTION ===== -->
<section class="py-16 bg-brand text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Find Your Perfect Scanner</h2>
                <p class="text-lg text-brand-light mb-6">Explore our full range of barcode scanning solutions for every industry</p>
                <a href="{{ route('products.index') }}" class="bg-white text-brand hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition inline-block">Browse Products →</a>
            </div>
            <div>
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Find a Reseller</h2>
                <p class="text-lg text-brand-light mb-6">Connect with authorized distributors and service centers in your region</p>
                <a href="{{ route('resellers') }}" class="bg-white text-brand hover:bg-gray-100 px-8 py-3 rounded-lg font-semibold transition inline-block">Find Location →</a>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.swiper-banner', {
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            loop: true,
            effect: 'fade',
            fadeEffect: { crossFade: true },
        });
    });
</script>
@endsection
