@props(['product'])

<a href="{{ route('products.show', $product->slug) }}"
   class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-xl transition border border-gray-100 flex flex-col">
    <div class="aspect-square bg-gray-50 flex items-center justify-center overflow-hidden">
        @if($product->getFirstMediaUrl('gallery'))
            <img src="{{ $product->getFirstMediaUrl('gallery', 'thumb') ?: $product->getFirstMediaUrl('gallery') }}"
                 alt="{{ $product->getTranslation('name', app()->getLocale()) }}"
                 class="w-full h-full object-contain p-4 group-hover:scale-105 transition duration-300">
        @else
            <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
            </svg>
        @endif
    </div>

    <div class="p-5 flex flex-col flex-1">
        @if($product->sku)
            <p class="text-xs text-gray-400 font-mono mb-1">{{ $product->sku }}</p>
        @endif
        <h3 class="font-bold text-neutral-950 group-hover:text-brand transition line-clamp-2 flex-1">
            {{ $product->getTranslation('name', app()->getLocale()) }}
        </h3>
        @if($product->getTranslation('short_description', app()->getLocale()))
            <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                {{ $product->getTranslation('short_description', app()->getLocale()) }}
            </p>
        @endif
        <div class="mt-4 flex items-center justify-between">
            <span class="text-sm font-semibold text-brand group-hover:underline">
                {{ __('site.view_details') ?? 'View Details' }} →
            </span>
        </div>
    </div>
</a>
