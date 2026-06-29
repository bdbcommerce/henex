<div>
    {{-- Modal overlay --}}
    <div x-data="{ open: @entangle('open') }"
         x-show="open"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @keydown.escape.window="$wire.close()"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-950/60 backdrop-blur-sm"
         x-cloak>

        <div @click.outside="$wire.close()"
             x-transition:enter="transition-all duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-7 relative">

            {{-- Close --}}
            <button wire:click="close" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            @if($sent)
            <div class="text-center py-6">
                <div class="text-5xl mb-4">✅</div>
                <h3 class="text-xl font-bold text-neutral-950 mb-2">{{ __('site.contact.sent_title') }}</h3>
                <p class="text-gray-500 text-sm mb-6">{{ __('site.contact.sent_subtitle') }}</p>
                <button wire:click="close" class="bg-brand text-white font-bold px-6 py-2.5 rounded-xl hover:bg-brand-dark transition">{{ __('site.contact.close') }}</button>
            </div>
            @else
            <div class="mb-5">
                <h3 class="text-xl font-bold text-neutral-950">{{ __('site.cta.request_quote') }}</h3>
                @if($productName)
                <p class="text-sm text-brand font-semibold mt-1">{{ $productName }}</p>
                @endif
            </div>
            <form wire:submit="submit" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.name') }} <span class="text-red-500">*</span></label>
                    <input wire:model="name" type="text" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm @error('name') border-red-400 @enderror">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.phone') }} <span class="text-red-500">*</span></label>
                    <input wire:model="phone" type="tel" placeholder="+998 __ ___ __ __" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm @error('phone') border-red-400 @enderror">
                    @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.email') }}</label>
                    <input wire:model="email" type="email" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.message') }}</label>
                    <textarea wire:model="message" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm" placeholder="{{ __('site.form.message_placeholder') }}"></textarea>
                </div>
                <button type="submit" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2">
                    <span wire:loading.remove>{{ __('site.form.send') }}</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        {{ __('site.form.sending') }}
                    </span>
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
