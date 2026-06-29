@extends('layouts.app')

@section('title', __('site.nav.products') . ' | Henex Uzbekistan')

@section('content')

{{-- Page Hero --}}
<section class="bg-neutral-950 text-white py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <x-site.breadcrumb :items="[
            [__('site.nav.home'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('home'))],
            [__('site.nav.products'), null],
        ]" dark />

        <h1 class="text-3xl sm:text-4xl font-extrabold mt-4 mb-2">
            {{ __('site.products.title') }}
        </h1>
        <p class="text-gray-400 text-lg max-w-2xl">
            {{ __('site.products.subtitle') }}
        </p>
    </div>
</section>

{{-- Catalog --}}
<section class="py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        @livewire('frontend.product-catalog', ['categorySlug' => request('category')])
    </div>
</section>

@endsection
