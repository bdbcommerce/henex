@extends('telegram.layout')

@section('content')
    <div class="flex items-center justify-between mb-4">
        <span class="text-xl font-extrabold" style="color:#FF8C42">HENEX</span>
        <div class="flex gap-1 text-xs">
            @foreach(['uz' => 'UZ', 'ru' => 'RU', 'en' => 'EN'] as $code => $label)
                <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                   class="px-2 py-1 rounded-md {{ app()->getLocale() === $code ? 'chip-on' : 'card' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <form method="GET" action="{{ route('tg.home') }}" class="mb-5">
        <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
        <input type="search" name="q" value="{{ $search }}" placeholder="🔍 {{ __('bot.webapp.search') }}"
               class="w-full card rounded-xl border-0 px-4 py-2.5 text-sm focus:ring-2 focus:ring-orange-300" style="color: var(--text)">
    </form>

    @if($search !== '')
        <div class="space-y-2 mb-6">
            @forelse($results as $product)
                @include('telegram.partials.product-row')
            @empty
                <p class="hint text-sm text-center py-6">{{ __('bot.webapp.no_results') }}</p>
            @endforelse
        </div>
    @else
        @if($featured->isNotEmpty())
            <h2 class="font-bold mb-2">⭐ {{ __('bot.webapp.featured') }}</h2>
            <div class="flex gap-3 overflow-x-auto no-scrollbar -mx-4 px-4 pb-2 mb-5 snap-x">
                @foreach($featured as $p)
                    <a href="{{ route('tg.product', ['product' => $p->slug, 'lang' => app()->getLocale()]) }}"
                       class="card rounded-xl p-2 w-36 shrink-0 snap-start active:opacity-70">
                        <img src="{{ $p->getFirstMediaUrl('gallery', 'thumb') ?: $p->getFirstMediaUrl('gallery') }}" alt="" loading="lazy"
                             class="w-full aspect-square object-contain rounded-lg bg-white mb-2">
                        <div class="text-sm font-semibold truncate">{{ $p->sku ?: $p->getTranslation('name', app()->getLocale()) }}</div>
                        <div class="hint text-xs line-clamp-2">{{ strip_tags($p->getTranslation('short_description', app()->getLocale())) }}</div>
                    </a>
                @endforeach
            </div>
        @endif

        <h2 class="font-bold mb-2">{{ __('bot.webapp.categories') }}</h2>
        <div class="grid grid-cols-2 gap-3">
            @foreach($categories as $cat)
                <a href="{{ route('tg.category', ['slug' => $cat->slug, 'lang' => app()->getLocale()]) }}"
                   class="card rounded-xl p-3 active:opacity-70 flex flex-col">
                    @if($img = $cat->getFirstMediaUrl('image'))
                        <img src="{{ $img }}" alt="" loading="lazy" class="w-full aspect-[4/3] object-contain rounded-lg bg-white mb-2">
                    @endif
                    <div class="text-sm font-semibold leading-tight">{{ $cat->getTranslation('name', app()->getLocale()) }}</div>
                    <div class="hint text-xs mt-1">{{ $cat->products_count }} {{ __('bot.webapp.products') }}</div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
