<div>
    @if($sent)
    <div class="text-center py-10">
        <div class="text-5xl mb-4">✅</div>
        <h3 class="text-xl font-bold text-neutral-950 mb-2">{{ __('site.contact.sent_title') }}</h3>
        <p class="text-gray-500">{{ __('site.contact.sent_subtitle') }}</p>
        <button wire:click="$set('sent', false)" class="mt-6 text-brand hover:underline text-sm">{{ __('site.contact.send_another') }}</button>
    </div>
    @else
    <form wire:submit="submit" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.name') }} <span class="text-red-500">*</span></label>
                <input wire:model="name" type="text" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.company') }}</label>
                <input wire:model="company" type="text" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.phone') }} <span class="text-red-500">*</span></label>
                <input wire:model="phone" type="tel" placeholder="+998 __ ___ __ __" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm @error('phone') border-red-400 @enderror">
                @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.email') }}</label>
                <input wire:model="email" type="email" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.product') }}</label>
            <select wire:model="product_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm">
                <option value="">— {{ __('site.form.product_optional') }} —</option>
                @foreach($products as $product)
                <option value="{{ $product->id }}">{{ $product->getTranslation('name', app()->getLocale()) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">{{ __('site.form.message') }}</label>
            <textarea wire:model="message" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:ring-brand focus:border-brand text-sm"></textarea>
        </div>
        <button type="submit"
                class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3.5 px-6 rounded-xl transition text-base flex items-center justify-center gap-2">
            <span wire:loading.remove>{{ __('site.form.send') }}</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                {{ __('site.form.sending') }}
            </span>
        </button>
    </form>
    @endif
</div>
