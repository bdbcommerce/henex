<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">{{ $industry?->exists ? 'Edit Industry' : 'New Industry' }}</h2>
        <a href="{{ route('admin.industries.index') }}" class="text-sm text-gray-500 hover:text-brand">← Back</a>
    </div>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach(['uz' => "O'zbek", 'ru' => 'Русский', 'en' => 'English'] as $code => $label)
            <div>
                <label class="text-xs font-semibold text-gray-500 block mb-1">Name — {{ $label }}@if($code === 'uz') <span class="text-red-400">*</span>@endif</label>
                <input type="text" wire:model="form.name.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm">
                @error("form.name.{$code}") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @endforeach
            <div>
                <label class="text-xs font-semibold text-gray-500 block mb-1">Emoji (shown on the bot button)</label>
                <input type="text" wire:model="form.emoji" placeholder="🛒" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <x-admin.slug-display :slug="$industry?->exists ? $industry->slug : null" source="English name" :follows="false" />
            <div class="pt-6"><label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_active" class="rounded border-gray-300 text-brand focus:ring-brand"> Active (shown in the bot)</label></div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-1">Recommended categories</h3>
            <p class="text-xs text-gray-500 mb-3">The bot's scanner advisor suggests products from these categories to clients of this industry.</p>
            @php
                $categoryOptions = $categories->flatMap(fn ($cat) => collect([['id' => $cat->id, 'name' => $cat->getTranslation('name', 'en')]])
                    ->concat($cat->children->map(fn ($ch) => ['id' => $ch->id, 'name' => $ch->getTranslation('name', 'en'), 'child' => true])))->values();
            @endphp
            <x-admin.multi-select :options="$categoryOptions" model="selectedCategories" placeholder="Select categories…" />
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">Save</button>
            <a href="{{ route('admin.industries.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm hover:bg-gray-50 transition">Cancel</a>
        </div>
    </form>
</div>
