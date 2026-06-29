<div>
    <h2 class="text-xl font-bold text-neutral-950 mb-6">Site Settings</h2>
    @if(session('success'))<div class="bg-green-50 text-green-700 text-sm px-4 py-3 rounded-lg mb-4">{{ session('success') }}</div>@endif
    <form wire:submit="save" class="space-y-5">
        @foreach([
            'site_phone' => 'Phone (primary)', 'site_phone2' => 'Phone (secondary)',
            'site_email' => 'Email', 'working_hours' => 'Working Hours',
            'address_uz' => 'Address (UZ)', 'address_ru' => 'Address (RU)', 'address_en' => 'Address (EN)',
            'social_telegram' => 'Telegram URL', 'social_instagram' => 'Instagram URL',
            'social_youtube' => 'YouTube URL', 'social_whatsapp' => 'WhatsApp URL',
        ] as $key => $label)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">{{ $label }}</label>
            <input type="text" wire:model="settings.{{ $key }}" class="w-full rounded-lg border-gray-300 text-sm">
        </div>
        @endforeach
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wide">Yandex Map Embed (iframe HTML)</label>
            <textarea wire:model="settings.yandex_map_embed" rows="4" class="w-full rounded-lg border-gray-300 text-sm font-mono"></textarea>
        </div>
        <button type="submit" class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">Save Settings</button>
    </form>
</div>
