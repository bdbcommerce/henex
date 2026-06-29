<div x-data="{ locale: 'uz' }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">{{ $product?->exists ? 'Edit Product' : 'New Product' }}</h2>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-brand">← Back</a>
    </div>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="block text-xs font-semibold text-gray-500 mb-1">SKU</label><input type="text" wire:model="form.sku" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="block text-xs font-semibold text-gray-500 mb-1">Slug</label><input type="text" wire:model="form.slug" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="block text-xs font-semibold text-gray-500 mb-1">Sort Order</label><input type="number" wire:model="form.sort_order" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_active"> Active</label>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_featured"> Featured</label>
            </div>
        </div>
        {{-- Locale tabs --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex gap-2 mb-5 border-b border-gray-200">
                @foreach(['uz' => "O'zbek", 'ru' => 'Русский', 'en' => 'English'] as $code => $label)
                <button type="button" @click="locale = '{{ $code }}'" :class="locale === '{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-400'" class="pb-2 px-3 text-sm transition">{{ $label }}</button>
                @endforeach
            </div>
            @foreach(['uz','ru','en'] as $code)
            <div x-show="locale === '{{ $code }}'">
                <div class="mb-3"><label class="text-xs font-semibold text-gray-500 block mb-1">Name ({{ $code }})</label><input type="text" wire:model="form.name.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm"></div>
                <div class="mb-3"><label class="text-xs font-semibold text-gray-500 block mb-1">Short Description ({{ $code }})</label><textarea wire:model="form.short_description.{{ $code }}" rows="2" class="w-full rounded-lg border-gray-300 text-sm"></textarea></div>
                <div class="mb-3"><label class="text-xs font-semibold text-gray-500 block mb-1">Meta Title ({{ $code }})</label><input type="text" wire:model="form.meta_title.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm"></div>
                <div><label class="text-xs font-semibold text-gray-500 block mb-1">Meta Description ({{ $code }})</label><textarea wire:model="form.meta_description.{{ $code }}" rows="2" class="w-full rounded-lg border-gray-300 text-sm"></textarea></div>
            </div>
            @endforeach
        </div>
        {{-- Categories --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-semibold mb-3 text-sm">Categories</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($categories as $cat)
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="selectedCategories" value="{{ $cat->id }}"> {{ $cat->getTranslation('name','en') }}</label>
                @endforeach
            </div>
        </div>
        {{-- Specs --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4"><h3 class="font-semibold text-sm">Specifications</h3><button type="button" wire:click="addSpec" class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg">+ Add row</button></div>
            @foreach($specs as $i => $spec)
            <div x-data="{ locale: 'uz' }" class="grid grid-cols-2 gap-3 mb-3 p-3 border border-gray-100 rounded-lg">
                <div>
                    <div class="flex gap-1 mb-1">@foreach(['uz','ru','en'] as $c)<button type="button" @click="locale='{{ $c }}'" :class="locale==='{{ $c }}' ? 'text-brand font-bold' : 'text-gray-400'" class="text-xs">{{ $c }}</button>@endforeach</div>
                    @foreach(['uz','ru','en'] as $c)<input x-show="locale==='{{ $c }}'" type="text" wire:model="specs.{{ $i }}.key.{{ $c }}" placeholder="Key ({{ $c }})" class="w-full rounded border-gray-300 text-xs">@endforeach
                </div>
                <div>
                    <div class="flex gap-1 mb-1">@foreach(['uz','ru','en'] as $c)<button type="button" @click="locale='{{ $c }}'" :class="locale==='{{ $c }}' ? 'text-brand font-bold' : 'text-gray-400'" class="text-xs">{{ $c }}</button>@endforeach</div>
                    @foreach(['uz','ru','en'] as $c)<input x-show="locale==='{{ $c }}'" type="text" wire:model="specs.{{ $i }}.value.{{ $c }}" placeholder="Value ({{ $c }})" class="w-full rounded border-gray-300 text-xs">@endforeach
                </div>
                <button type="button" wire:click="removeSpec({{ $i }})" class="text-xs text-red-400 hover:text-red-600 col-span-2 text-right">Remove</button>
            </div>
            @endforeach
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">Save Product</button>
            <a href="{{ route('admin.products.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm hover:bg-gray-50 transition">Cancel</a>
        </div>
    </form>
</div>
