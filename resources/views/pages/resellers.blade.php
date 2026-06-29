@extends('layouts.app')

@section('title', __('site.nav.where_to_buy') . ' | Henex Uzbekistan')

@section('meta')
    <meta name="description" content="{{ __('site.resellers.meta_description') }}">
@endsection

@section('content')

{{-- Page Hero --}}
<section class="bg-neutral-950 text-white py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            [__('site.nav.home'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('home'))],
            [__('site.nav.where_to_buy'), null],
        ]" dark />

        <h1 class="text-3xl sm:text-4xl font-extrabold mt-4 mb-3">
            {{ __('site.resellers.title') }}
        </h1>
        <p class="text-gray-400 text-lg max-w-2xl">
            {{ __('site.resellers.subtitle') }}
        </p>
    </div>
</section>

{{-- Stats Bar --}}
<div class="bg-brand text-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 divide-x divide-white/20">
            <div class="text-center py-5">
                <div class="text-3xl font-extrabold">14</div>
                <div class="text-sm text-white/80 mt-1">{{ __('site.resellers.regions') }}</div>
            </div>
            <div class="text-center py-5">
                <div class="text-3xl font-extrabold">{{ $resellerCount ?? '50+' }}</div>
                <div class="text-sm text-white/80 mt-1">{{ __('site.resellers.partners') }}</div>
            </div>
            <div class="text-center py-5">
                <div class="text-3xl font-extrabold">{{ $serviceCenterCount ?? '10+' }}</div>
                <div class="text-sm text-white/80 mt-1">{{ __('site.resellers.service_centers') }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Reseller Finder Component --}}
<section class="py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        @livewire('frontend.reseller-finder', ['regionSlug' => request('region')])
    </div>
</section>

{{-- Become a Reseller CTA --}}
<section class="py-16 bg-gray-50 border-t border-gray-200">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-block bg-brand/10 text-brand font-semibold text-sm rounded-full px-4 py-1.5 mb-4">
                {{ __('site.resellers.partner_badge') }}
            </div>
            <h2 class="text-3xl font-extrabold text-neutral-950 mb-4">
                {{ __('site.resellers.become_reseller_title') }}
            </h2>
            <p class="text-gray-600 text-lg mb-8">
                {{ __('site.resellers.become_reseller_subtitle') }}
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ LaravelLocalization::getLocalizedURL(app()->getLocale(), route('contact')) }}"
                   class="bg-brand hover:bg-brand-dark text-white font-bold py-4 px-8 rounded-xl transition text-lg">
                    {{ __('site.resellers.contact_us') }}
                </a>
                <a href="tel:{{ settings('site_phone') }}"
                   class="border-2 border-neutral-950 hover:bg-neutral-950 hover:text-white text-neutral-950 font-bold py-4 px-8 rounded-xl transition text-lg">
                    {{ settings('site_phone') }}
                </a>
            </div>
        </div>
    </div>
</section>

@endsection
