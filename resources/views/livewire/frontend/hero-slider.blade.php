<div x-data="{
    active: 0,
    total: {{ count($slides) }},
    autoplay: null,
    init() {
        if (this.total > 1) {
            this.autoplay = setInterval(() => { this.active = (this.active + 1) % this.total; }, 5000);
        }
    },
    go(i) { this.active = i; clearInterval(this.autoplay); }
}" class="relative overflow-hidden bg-neutral-950 min-h-[520px] flex items-center">

    @forelse($slides as $i => $slide)
    <div x-show="active === {{ $i }}"
         x-transition:enter="transition-opacity duration-700"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="absolute inset-0 flex items-center">
        @if($slide->getFirstMediaUrl('slide'))
        <img src="{{ $slide->getFirstMediaUrl('slide') }}" alt="{{ $slide->getTranslation('title', app()->getLocale()) }}"
             class="absolute inset-0 w-full h-full object-cover opacity-40">
        @endif
        <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <h2 class="text-4xl sm:text-6xl font-extrabold text-white mb-4 leading-tight max-w-2xl">
                {{ $slide->getTranslation('title', app()->getLocale()) }}
            </h2>
            @if($slide->getTranslation('subtitle', app()->getLocale()))
            <p class="text-gray-300 text-xl mb-8 max-w-xl">
                {{ $slide->getTranslation('subtitle', app()->getLocale()) }}
            </p>
            @endif
            @if($slide->link)
            <a href="{{ $slide->link }}"
               class="inline-block bg-brand hover:bg-brand-dark text-white font-bold py-4 px-8 rounded-xl transition text-lg">
                {{ $slide->getTranslation('button_text', app()->getLocale()) ?: __('site.cta.learn_more') }}
            </a>
            @endif
        </div>
    </div>
    @empty
    {{-- Fallback hero when no slides --}}
    <div class="relative z-10 container mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h2 class="text-4xl sm:text-6xl font-extrabold text-white mb-4">{{ __('site.hero.title') }}</h2>
        <p class="text-gray-300 text-xl mb-8">{{ __('site.hero.subtitle') }}</p>
        <a href="{{ route('products.index') }}" class="inline-block bg-brand hover:bg-brand-dark text-white font-bold py-4 px-8 rounded-xl transition text-lg">
            {{ __('site.cta.view_products') }}
        </a>
    </div>
    @endforelse

    {{-- Dots navigation --}}
    @if(count($slides) > 1)
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-20">
        @foreach($slides as $i => $slide)
        <button @click="go({{ $i }})"
                :class="active === {{ $i }} ? 'bg-brand w-6' : 'bg-white/40 w-2'"
                class="h-2 rounded-full transition-all duration-300"></button>
        @endforeach
    </div>
    @endif
</div>
