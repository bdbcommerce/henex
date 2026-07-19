<div>
    <h2 class="text-xl font-bold text-neutral-950 mb-6">Site Settings</h2>

    <form wire:submit="save" class="space-y-6">

        {{-- Contacts --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Contacts</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['site_phone' => 'Phone (primary)', 'site_phone2' => 'Phone (secondary)', 'site_email' => 'Email', 'working_hours' => 'Working Hours'] as $key => $label)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">{{ $label }}</label>
                    <input type="text" wire:model="settings.{{ $key }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                </div>
                @endforeach
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                @foreach(['address_uz' => 'Address (UZ)', 'address_ru' => 'Address (RU)', 'address_en' => 'Address (EN)'] as $key => $label)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">{{ $label }}</label>
                    <input type="text" wire:model="settings.{{ $key }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Social / Messaging --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Social & Messaging</h3>
            <p class="text-xs text-gray-500 mb-4">Enter full URLs (https://...). The Telegram link is also used for the floating chat button.</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['social_telegram' => 'Telegram URL', 'social_whatsapp' => 'WhatsApp URL', 'social_instagram' => 'Instagram URL', 'social_youtube' => 'YouTube URL'] as $key => $label)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">{{ $label }}</label>
                    <input type="url" wire:model="settings.{{ $key }}"
                           placeholder="https://..."
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Analytics --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Analytics</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Google Analytics 4 ID</label>
                    <input type="text" wire:model="settings.analytics_ga4_id"
                           placeholder="G-XXXXXXXXXX"
                           class="w-full rounded-lg border-gray-300 text-sm font-mono focus:border-brand focus:ring-brand">
                    <p class="text-xs text-gray-400 mt-1">Leave blank to disable GA4 tracking.</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Yandex Metrica Counter ID</label>
                    <input type="text" wire:model="settings.analytics_yandex_id"
                           placeholder="12345678"
                           class="w-full rounded-lg border-gray-300 text-sm font-mono focus:border-brand focus:ring-brand">
                    <p class="text-xs text-gray-400 mt-1">Numeric ID only. Leave blank to disable.</p>
                </div>
            </div>
        </div>

        {{-- Search Console Verification --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Search Console Verification</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Google Verification Code</label>
                    <input type="text" wire:model="settings.google_verification"
                           placeholder="e.g. abc123xyz..."
                           class="w-full rounded-lg border-gray-300 text-sm font-mono focus:border-brand focus:ring-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Yandex Verification Code</label>
                    <input type="text" wire:model="settings.yandex_verification"
                           placeholder="e.g. abc123xyz..."
                           class="w-full rounded-lg border-gray-300 text-sm font-mono focus:border-brand focus:ring-brand">
                </div>
            </div>
        </div>

        {{-- Maps --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Yandex Map Embed</h3>
            <label class="block text-xs font-semibold text-gray-500 mb-1">iframe HTML</label>
            <textarea wire:model="settings.yandex_map_embed" rows="4"
                      placeholder='<iframe src="https://yandex.uz/map-widget/..." ...></iframe>'
                      class="w-full rounded-lg border-gray-300 text-sm font-mono focus:border-brand focus:ring-brand"></textarea>
        </div>

        <div class="flex items-center gap-3 pb-4">
            <button type="submit"
                    wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait"
                    class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">
                <span wire:loading.remove wire:target="save">Save Settings</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>

    </form>
</div>
