@extends('telegram.layout')

@php($l = app()->getLocale())
@section('title', $product->sku ?: $product->getTranslation('name', $l))

@section('content')
    {{-- Gallery: swipeable --}}
    @php($images = $product->getMedia('gallery'))
    @if($images->isNotEmpty())
        <div class="flex overflow-x-auto no-scrollbar snap-x snap-mandatory -mx-4 mb-4 bg-white">
            @foreach($images as $img)
                <img src="{{ $img->getUrl('medium') ?: $img->getUrl() }}" alt="" loading="{{ $loop->first ? 'eager' : 'lazy' }}"
                     class="w-full shrink-0 snap-center aspect-square object-contain">
            @endforeach
        </div>
    @endif

    <h1 class="text-lg font-bold leading-tight">{{ $product->getTranslation('name', $l) }}</h1>
    @if($product->sku)<div class="hint text-sm">{{ $product->sku }}</div>@endif
    <p class="text-sm mt-2">{{ strip_tags($product->getTranslation('short_description', $l)) }}</p>

    @if($product->attributeValues->isNotEmpty())
        <div class="mt-4 space-y-1.5">
            @foreach($product->attributeValues->groupBy('attribute_id') as $values)
                <div class="flex gap-2 text-sm">
                    <span class="hint shrink-0">{{ $values->first()->attribute->getTranslation('name', $l) }}:</span>
                    <span>{{ $values->map(fn ($v) => $v->getTranslation('value', $l))->join(', ') }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if($desc = $product->getTranslation('description', $l))
        <h2 class="font-bold mt-5 mb-2">{{ __('bot.webapp.description') }}</h2>
        <div class="prose-tg">{!! $desc !!}</div>
    @endif


    {{-- Quote request sheet, opened by Telegram's MainButton (or the fallback button outside Telegram) --}}
    <div x-data="quote" class="mt-6">
        <button type="button" x-show="!inTelegram" @click="open = true" class="btn w-full rounded-xl py-3 font-semibold">
            {{ __('bot.webapp.request_quote') }}
        </button>

        <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 bg-black/40 flex items-end" style="display:none" @click.self="open = false">
            <div class="w-full max-w-xl mx-auto rounded-t-2xl p-4 pb-8" style="background: var(--bg)">
                <h3 class="font-bold mb-1">{{ __('bot.webapp.request_quote') }}</h3>
                <p class="hint text-xs mb-3">{{ $product->sku }} — {{ $product->getTranslation('name', $l) }}</p>
                <textarea x-model="message" x-ref="msg" rows="4" placeholder="{{ __('bot.webapp.quote_placeholder') }}"
                          class="w-full card rounded-xl border-0 text-sm" style="color: var(--text)"></textarea>
                <p x-show="error" x-text="error" class="text-red-500 text-xs mt-2"></p>
                <button type="button" @click="send" :disabled="sending" class="btn w-full rounded-xl py-3 mt-3 font-semibold disabled:opacity-60">
                    {{ __('bot.webapp.send') }}
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('quote', () => ({
        open: false,
        sending: false,
        error: '',
        message: '',
        inTelegram: !!window.Telegram?.WebApp?.initData,
        init() {
            const tg = window.Telegram?.WebApp;
            if (this.inTelegram) {
                tg.MainButton.setParams({ text: @js(__('bot.webapp.request_quote')) }).show();
                tg.MainButton.onClick(() => { this.open = true; this.$nextTick(() => this.$refs.msg.focus()); });
            }
        },
        async send() {
            const tg = window.Telegram?.WebApp;
            this.error = '';
            if (!this.inTelegram) { this.error = @js(__('bot.webapp.open_in_telegram')); return; }
            if (this.message.trim().length < 3) { this.$refs.msg.focus(); return; }
            this.sending = true;
            try {
                const res = await fetch(@js(route('tg.inquiry', ['lang' => $l])), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ init_data: tg.initData, product_id: {{ $product->id }}, message: this.message }),
                });
                const data = await res.json().catch(() => ({}));
                if (data.ok) {
                    this.open = false; this.message = '';
                    tg.showAlert(@js(__('bot.webapp.sent')));
                } else {
                    this.error = data.error === 'not_registered' ? @js(__('bot.webapp.not_registered')) : @js(__('bot.webapp.error'));
                }
            } catch (e) {
                this.error = @js(__('bot.webapp.error'));
            } finally {
                this.sending = false;
            }
        },
    }));
});
</script>
@endpush
