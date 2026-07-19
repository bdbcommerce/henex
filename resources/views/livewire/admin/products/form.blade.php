<div x-data="{ locale: 'uz' }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">{{ $product?->exists ? 'Edit Product' : 'New Product' }}</h2>
        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-500 hover:text-brand">← Back</a>
    </div>
    <form wire:submit="save" class="space-y-6">

        {{-- Basic fields --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Basic Info</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">SKU</label>
                    <input type="text" wire:model="form.sku" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                    @error('form.sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Slug <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="form.slug" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                    @error('form.slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Sort Order</label>
                    <input type="number" wire:model="form.sort_order" class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                </div>
            </div>
            <div class="flex items-center gap-6 mt-4">
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" wire:model="form.is_active" class="rounded border-gray-300 text-brand focus:ring-brand">
                    <span>Active</span>
                </label>
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" wire:model="form.is_featured" class="rounded border-gray-300 text-brand focus:ring-brand">
                    <span>Featured on homepage</span>
                </label>
            </div>
        </div>

        {{-- Translatable content --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex gap-1 mb-5 border-b border-gray-200">
                @foreach(['uz' => "O'zbek", 'ru' => 'Русский', 'en' => 'English'] as $code => $label)
                <button type="button" @click="locale = '{{ $code }}'"
                        :class="locale === '{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-400 hover:text-gray-600'"
                        class="pb-2 px-4 text-sm transition">{{ $label }}</button>
                @endforeach
            </div>

            @foreach(['uz', 'ru', 'en'] as $code)
            <div x-show="locale === '{{ $code }}'" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">
                        Name ({{ strtoupper($code) }})@if($code === 'uz') <span class="text-red-400">*</span>@endif
                    </label>
                    <input type="text" wire:model="form.name.{{ $code }}"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                    @error("form.name.{$code}") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Short Description ({{ strtoupper($code) }})</label>
                    <textarea wire:model="form.short_description.{{ $code }}" rows="2"
                              class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Full Description ({{ strtoupper($code) }})</label>
                    <textarea wire:model="form.description.{{ $code }}" rows="8"
                              placeholder="Full product description (HTML allowed)…"
                              class="w-full rounded-lg border-gray-300 text-sm font-mono focus:border-brand focus:ring-brand"></textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Meta Title ({{ strtoupper($code) }})</label>
                        <input type="text" wire:model="form.meta_title.{{ $code }}"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Meta Description ({{ strtoupper($code) }})</label>
                        <textarea wire:model="form.meta_description.{{ $code }}" rows="2"
                                  class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand"></textarea>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Categories --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">Categories</h3>
            <div class="flex flex-wrap gap-x-6 gap-y-3">
                @foreach($categories as $cat)
                <label class="flex items-center gap-2 text-sm cursor-pointer">
                    <input type="checkbox" wire:model="selectedCategories" value="{{ $cat->id }}"
                           class="rounded border-gray-300 text-brand focus:ring-brand">
                    <span>{{ $cat->getTranslation('name', 'en') }}</span>
                </label>
                @foreach($cat->children as $child)
                <label class="flex items-center gap-2 text-sm cursor-pointer ml-4 text-gray-500">
                    <input type="checkbox" wire:model="selectedCategories" value="{{ $child->id }}"
                           class="rounded border-gray-300 text-brand focus:ring-brand">
                    <span>↳ {{ $child->getTranslation('name', 'en') }}</span>
                </label>
                @endforeach
                @endforeach
            </div>
        </div>

        {{-- Images --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Gallery Images</h3>

            @if(count($existingImages))
            <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-4">
                @foreach($existingImages as $img)
                <div class="relative group">
                    <img src="{{ $img['url'] }}" alt="{{ $img['name'] }}"
                         class="w-full aspect-square object-cover rounded-lg border border-gray-200">
                    <button type="button" wire:click="deleteImage({{ $img['id'] }})"
                            wire:loading.attr="disabled"
                            class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition leading-none">
                        &times;
                    </button>
                </div>
                @endforeach
            </div>
            @endif

            <label class="block">
                <span class="block text-xs font-semibold text-gray-500 mb-1">
                    {{ count($existingImages) ? 'Add more images' : 'Upload images' }}
                </span>
                <input type="file" wire:model="newImages" multiple accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand/10 file:text-brand hover:file:bg-brand/20 transition">
                <div wire:loading wire:target="newImages" class="text-xs text-gray-400 mt-1">Uploading…</div>
            </label>
            @error('newImages.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

            @if($newImages)
            <p class="text-xs text-green-600 mt-2">{{ count($newImages) }} file(s) selected — will save on submit.</p>
            @endif
        </div>

        {{-- Specifications --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Specifications</h3>
                <button type="button" wire:click="addSpec"
                        class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg transition">
                    + Add row
                </button>
            </div>

            @forelse($specs as $i => $spec)
            <div x-data="{ locale: 'uz' }" class="grid grid-cols-2 gap-3 mb-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                <div>
                    <div class="flex gap-1 mb-1">
                        @foreach(['uz','ru','en'] as $c)
                        <button type="button" @click="locale='{{ $c }}'"
                                :class="locale==='{{ $c }}' ? 'text-brand font-bold' : 'text-gray-400'"
                                class="text-xs px-1 transition">{{ $c }}</button>
                        @endforeach
                    </div>
                    @foreach(['uz','ru','en'] as $c)
                    <input x-show="locale==='{{ $c }}'" type="text"
                           wire:model="specs.{{ $i }}.key.{{ $c }}"
                           placeholder="Key ({{ $c }})"
                           class="w-full rounded border-gray-300 text-xs focus:border-brand focus:ring-brand">
                    @endforeach
                </div>
                <div>
                    <div class="flex gap-1 mb-1">
                        @foreach(['uz','ru','en'] as $c)
                        <button type="button" @click="locale='{{ $c }}'"
                                :class="locale==='{{ $c }}' ? 'text-brand font-bold' : 'text-gray-400'"
                                class="text-xs px-1 transition">{{ $c }}</button>
                        @endforeach
                    </div>
                    @foreach(['uz','ru','en'] as $c)
                    <input x-show="locale==='{{ $c }}'" type="text"
                           wire:model="specs.{{ $i }}.value.{{ $c }}"
                           placeholder="Value ({{ $c }})"
                           class="w-full rounded border-gray-300 text-xs focus:border-brand focus:ring-brand">
                    @endforeach
                </div>
                <button type="button" wire:click="removeSpec({{ $i }})"
                        class="col-span-2 text-xs text-red-400 hover:text-red-600 text-right transition">
                    Remove row
                </button>
            </div>
            @empty
            <p class="text-sm text-gray-400 text-center py-4">No specifications yet. Click "+ Add row" to start.</p>
            @endforelse
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pb-4">
            <button type="submit"
                    wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait"
                    class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">
                <span wire:loading.remove wire:target="save">{{ $product?->exists ? 'Update Product' : 'Create Product' }}</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
            <a href="{{ route('admin.products.index') }}"
               class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>

    </form>
</div>
