@extends('layouts.app')

@section('title', __('site.nav.contact') . ' | Henex Uzbekistan')

@section('meta')
    <meta name="description" content="{{ __('site.contact.meta_description') }}">
@endsection

@section('content')

{{-- Page Hero --}}
<section class="bg-neutral-950 text-white py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <x-site.breadcrumb :items="[
            [__('site.nav.home'), LaravelLocalization::getLocalizedURL(app()->getLocale(), route('home'))],
            [__('site.nav.contact'), null],
        ]" dark />
        <h1 class="text-3xl sm:text-4xl font-extrabold mt-4 mb-2">
            {{ __('site.contact.title') }}
        </h1>
        <p class="text-gray-400 text-lg max-w-xl">{{ __('site.contact.subtitle') }}</p>
    </div>
</section>

{{-- Contact Content --}}
<section class="py-14 bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

            {{-- Contact Info --}}
            <div class="lg:col-span-1 flex flex-col gap-6">

                {{-- Phone --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="w-12 h-12 bg-brand/10 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-neutral-950 mb-1">{{ __('site.contact.phone') }}</h3>
                    <a href="tel:{{ settings('site_phone') }}"
                       class="text-brand font-semibold text-lg hover:underline block">
                        {{ settings('site_phone') }}
                    </a>
                    @if(settings('site_phone2'))
                    <a href="tel:{{ settings('site_phone2') }}"
                       class="text-gray-500 hover:text-brand text-sm block mt-1">
                        {{ settings('site_phone2') }}
                    </a>
                    @endif
                    <p class="text-gray-400 text-sm mt-2">{{ settings('working_hours', __('site.contact.working_hours_default')) }}</p>
                </div>

                {{-- Email --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-neutral-950 mb-1">{{ __('site.contact.email') }}</h3>
                    <a href="mailto:{{ settings('site_email') }}"
                       class="text-blue-600 font-semibold hover:underline break-all">
                        {{ settings('site_email') }}
                    </a>
                </div>

                {{-- Address --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-neutral-950 mb-1">{{ __('site.contact.address') }}</h3>
                    <p class="text-gray-600 text-sm leading-relaxed">{{ settings('address_' . app()->getLocale(), settings('address_ru')) }}</p>
                </div>

                {{-- Social Links --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
                    <h3 class="font-bold text-neutral-950 mb-4">{{ __('site.contact.follow_us') }}</h3>
                    <div class="flex gap-3">
                        @if(settings('social_telegram'))
                        <a href="{{ settings('social_telegram') }}" target="_blank" rel="noopener"
                           class="w-10 h-10 bg-[#229ED9] text-white rounded-xl flex items-center justify-center hover:opacity-90 transition">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                            </svg>
                        </a>
                        @endif
                        @if(settings('social_instagram'))
                        <a href="{{ settings('social_instagram') }}" target="_blank" rel="noopener"
                           class="w-10 h-10 bg-gradient-to-br from-purple-600 to-pink-500 text-white rounded-xl flex items-center justify-center hover:opacity-90 transition">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                        @endif
                        @if(settings('social_youtube'))
                        <a href="{{ settings('social_youtube') }}" target="_blank" rel="noopener"
                           class="w-10 h-10 bg-red-600 text-white rounded-xl flex items-center justify-center hover:opacity-90 transition">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>

            </div>

            {{-- Contact Form --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                    <h2 class="text-2xl font-bold text-neutral-950 mb-2">{{ __('site.contact.form_title') }}</h2>
                    <p class="text-gray-500 mb-8">{{ __('site.contact.form_subtitle') }}</p>
                    @livewire('frontend.contact-form')
                </div>
            </div>

        </div>
    </div>
</section>

{{-- Map (Yandex Maps iframe) --}}
@if(settings('yandex_map_embed'))
<section class="bg-white border-t border-gray-200">
    <div class="h-[400px] w-full">
        {!! settings('yandex_map_embed') !!}
    </div>
</section>
@endif

@endsection
