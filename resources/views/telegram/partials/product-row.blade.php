<a href="{{ route('tg.product', ['product' => $product->slug, 'lang' => app()->getLocale()]) }}"
   class="card flex gap-3 rounded-xl p-2.5 active:opacity-70 transition">
    @if($thumb = $product->getFirstMediaUrl('gallery', 'thumb') ?: $product->getFirstMediaUrl('gallery'))
        <img src="{{ $thumb }}" alt="" loading="lazy" class="w-20 h-20 rounded-lg object-contain bg-white shrink-0">
    @else
        <div class="w-20 h-20 rounded-lg bg-black/5 shrink-0"></div>
    @endif
    <div class="min-w-0 py-0.5">
        <div class="font-semibold text-sm truncate">{{ $product->getTranslation('name', app()->getLocale()) }}</div>
        @if($product->sku)<div class="hint text-xs">{{ $product->sku }}</div>@endif
        <div class="hint text-xs mt-1 line-clamp-2">{{ strip_tags($product->getTranslation('short_description', app()->getLocale())) }}</div>
    </div>
</a>
