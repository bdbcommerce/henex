@extends('telegram.layout')

@php($l = app()->getLocale())
@section('title', __('bot.webapp.resellers'))

@section('content')
    <h1 class="text-lg font-bold mb-3">📍 {{ __('bot.webapp.resellers') }}</h1>

    <div x-data="{ region: 'all' }">
        <div class="flex gap-2 overflow-x-auto no-scrollbar -mx-4 px-4 pb-3 mb-2">
            <button type="button" @click="region = 'all'" :class="region === 'all' ? 'chip-on' : 'card'"
                    class="shrink-0 px-3 py-1.5 rounded-full text-xs font-medium">{{ __('bot.webapp.all_regions') }}</button>
            @foreach($regions as $region)
                <button type="button" @click="region = '{{ $region->slug }}'" :class="region === '{{ $region->slug }}' ? 'chip-on' : 'card'"
                        class="shrink-0 px-3 py-1.5 rounded-full text-xs font-medium">
                    {{ $region->getTranslation('name', $l) }}<span class="opacity-60"> · {{ $region->resellers->count() }}</span>
                </button>
            @endforeach
        </div>

        @foreach($regions as $region)
            <section x-show="region === 'all' || region === '{{ $region->slug }}'" class="mb-5">
                <h2 class="hint text-xs font-semibold uppercase tracking-wide mb-2">{{ $region->getTranslation('name', $l) }}</h2>
                @forelse($region->resellers as $r)
                    <div class="card rounded-xl p-3 mb-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="font-semibold text-sm">{{ $r->name }}</div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full chip-on shrink-0">{{ __('bot.webapp.type_' . $r->type) }}</span>
                        </div>
                        @if($addr = $r->getTranslation('address', $l))<div class="hint text-xs mt-1">{{ $addr }}</div>@endif
                        <div class="flex flex-wrap gap-3 mt-2 text-xs font-medium">
                            @if($r->phone)<a href="tel:{{ preg_replace('/[^\d+]/', '', $r->phone) }}" class="link">📞 {{ $r->phone }}</a>@endif
                            @if($r->latitude && $r->longitude)
                                <a href="https://yandex.uz/maps/?pt={{ $r->longitude }},{{ $r->latitude }}&z=16&l=map" target="_blank" class="link">🗺 {{ __('bot.webapp.map') }}</a>
                            @endif
                            @if($r->website)<a href="{{ $r->website }}" target="_blank" class="link">🌐 {{ __('bot.webapp.website') }}</a>@endif
                        </div>
                    </div>
                @empty
                    <p x-show="region !== 'all'" class="hint text-sm">{{ __('bot.webapp.no_resellers') }}</p>
                @endforelse
            </section>
        @endforeach
    </div>
@endsection
