<div x-data="{ locale: 'uz' }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">{{ $attribute?->exists ? 'Edit Attribute' : 'New Attribute' }}</h2>
        <a href="{{ route('admin.attributes.index') }}" class="text-sm text-gray-500 hover:text-brand">← Back</a>
    </div>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-admin.slug-display :slug="$attribute?->exists ? $attribute->slug : null" source="English name" :follows="false" />
            <div>
                <label class="text-xs font-semibold text-gray-500 block mb-1">Sort Order</label>
                <input type="number" wire:model="form.sort_order" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="space-y-2 pt-1">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_active" class="rounded border-gray-300 text-brand focus:ring-brand"> Active</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_multiple" class="rounded border-gray-300 text-brand focus:ring-brand"> Product can have several values</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_filterable" class="rounded border-gray-300 text-brand focus:ring-brand"> Use as catalog filter</label>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex gap-2 mb-5 border-b border-gray-200">
                @foreach(['uz'=>"O'zbek",'ru'=>'Русский','en'=>'English'] as $code => $label)
                <button type="button" @click="locale='{{ $code }}'" :class="locale==='{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-400'" class="pb-2 px-3 text-sm transition">{{ $label }}</button>
                @endforeach
            </div>

            @foreach(['uz','ru','en'] as $code)
            <div x-show="locale==='{{ $code }}'" class="mb-6">
                <label class="text-xs font-semibold text-gray-500 block mb-1">Attribute name ({{ $code }})@if($code === 'uz') <span class="text-red-400">*</span>@endif</label>
                <input type="text" wire:model="form.name.{{ $code }}" placeholder="{{ ['uz' => 'Interfeys', 'ru' => 'Интерфейс', 'en' => 'Interface'][$code] }}" class="w-full rounded-lg border-gray-300 text-sm">
                @error("form.name.{$code}") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            @endforeach

            <div class="flex items-center justify-between mb-3">
                <h3 class="text-sm font-semibold text-gray-700">Values <span class="font-normal text-gray-400">(<span x-text="locale"></span>)</span></h3>
                <button type="button" wire:click="addValue" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                    <x-admin.icon name="plus" class="w-3.5 h-3.5" /> Add value
                </button>
            </div>

            <div class="space-y-2">
                @foreach($values as $i => $row)
                <div class="flex items-start gap-2" wire:key="value-{{ $row['id'] ?? 'new-'.$i }}">
                    <span class="w-6 pt-2 text-xs text-gray-400 text-right">{{ $i + 1 }}</span>
                    <div class="flex-1">
                        @foreach(['uz','ru','en'] as $code)
                        <input x-show="locale==='{{ $code }}'" type="text" wire:model="values.{{ $i }}.value.{{ $code }}"
                               placeholder="{{ ['uz' => 'Simsiz', 'ru' => 'Беспроводной', 'en' => 'Wireless'][$code] }}"
                               class="w-full rounded-lg border-gray-300 text-sm @error("values.{$i}.value.{$code}") border-red-500 @enderror">
                        @endforeach
                        @error("values.{$i}.value.uz") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="button" wire:click="moveValue({{ $i }}, -1)" @disabled($loop->first) class="p-2 text-gray-400 hover:text-gray-700 disabled:opacity-30" title="Move up">↑</button>
                    <button type="button" wire:click="moveValue({{ $i }}, 1)" @disabled($loop->last) class="p-2 text-gray-400 hover:text-gray-700 disabled:opacity-30" title="Move down">↓</button>
                    <button type="button" wire:click="removeValue({{ $i }})" class="p-2 text-red-400 hover:text-red-600" title="Remove">
                        <x-admin.icon name="trash" class="w-4 h-4" />
                    </button>
                </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 mt-3">Removing a value also removes it from every product that uses it.</p>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">Save</button>
            <a href="{{ route('admin.attributes.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm hover:bg-gray-50 transition">Cancel</a>
        </div>
    </form>
</div>
