<div>
    {{-- Region tabs --}}
    <div class="overflow-x-auto pb-2 mb-6 scrollbar-hide">
        <div class="flex gap-2 min-w-max">
            <button wire:click="$set('region', '')"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold transition whitespace-nowrap {{ $region === '' ? 'bg-brand text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-brand hover:text-brand' }}">
                {{ __('site.resellers.all_regions') }}
            </button>
            @foreach($regions as $r)
            <button wire:click="$set('region', '{{ $r->slug }}')"
                    class="px-4 py-2.5 rounded-xl text-sm font-semibold transition whitespace-nowrap {{ $region === $r->slug ? 'bg-brand text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:border-brand hover:text-brand' }}">
                {{ $r->name }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Type filter --}}
    <div class="flex gap-2 mb-8">
        @foreach(['' => __('site.resellers.type.all'), 'reseller' => __('site.resellers.type.reseller'), 'service_center' => __('site.resellers.type.service_center')] as $val => $label)
        <button wire:click="$set('type', '{{ $val }}')"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $type === $val ? 'bg-neutral-950 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- Results --}}
    @if($resellers->isEmpty())
    <div class="text-center py-16 bg-white rounded-2xl border border-gray-200">
        <div class="text-5xl mb-4">📍</div>
        <p class="text-gray-500 text-lg">{{ __('site.resellers.none_found') }}</p>
        <a href="{{ route('contact') }}" class="inline-block mt-4 text-brand font-semibold hover:underline">{{ __('site.resellers.contact_us') }}</a>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($resellers as $reseller)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 hover:shadow-md transition-shadow">
            <div class="flex items-start justify-between gap-3 mb-4">
                <h3 class="font-bold text-neutral-950 text-lg leading-snug">{{ $reseller->name }}</h3>
                <span class="flex-shrink-0 text-xs font-bold px-2.5 py-1 rounded-full
                    {{ $reseller->type === 'service_center' ? 'bg-blue-50 text-blue-700' : ($reseller->type === 'both' ? 'bg-purple-50 text-purple-700' : 'bg-green-50 text-green-700') }}">
                    {{ __('site.resellers.type.' . $reseller->type) }}
                </span>
            </div>
            @if($reseller->getTranslation('address', app()->getLocale()))
            <p class="text-gray-500 text-sm mb-3 flex items-start gap-2">
                <svg class="w-4 h-4 flex-shrink-0 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $reseller->getTranslation('address', app()->getLocale()) }}
            </p>
            @endif
            <div class="flex flex-col gap-2">
                @if($reseller->phone)
                <a href="tel:{{ $reseller->phone }}" class="flex items-center gap-2 text-brand font-semibold text-sm hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    {{ $reseller->phone }}
                </a>
                @endif
                @if($reseller->website)
                <a href="{{ $reseller->website }}" target="_blank" rel="noopener" class="flex items-center gap-2 text-blue-600 text-sm hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    {{ parse_url($reseller->website, PHP_URL_HOST) }}
                </a>
                @endif
                @if($reseller->latitude && $reseller->longitude)
                <a href="https://yandex.uz/maps/?pt={{ $reseller->longitude }},{{ $reseller->latitude }}&z=16&l=map" target="_blank" rel="noopener"
                   class="text-xs text-gray-400 hover:text-brand transition">
                    🗺 {{ __('site.resellers.open_map') }}
                </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
