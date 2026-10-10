@extends('telegram.layout')

@section('title', $category->getTranslation('name', app()->getLocale()))

@section('content')
    <h1 class="text-lg font-bold mb-1">{{ $category->getTranslation('name', app()->getLocale()) }}</h1>
    <p class="hint text-xs mb-4">{{ $products->count() }} {{ __('bot.webapp.products') }}</p>

    <div class="space-y-2">
        @forelse($products as $product)
            @include('telegram.partials.product-row')
        @empty
            <p class="hint text-sm text-center py-6">{{ __('bot.webapp.no_results') }}</p>
        @endforelse
    </div>
@endsection
