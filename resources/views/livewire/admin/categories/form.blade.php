<div x-data="{ locale: 'uz' }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">{{ $category?->exists ? 'Edit Category' : 'New Category' }}</h2>
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-500 hover:text-brand">← Back</a>
    </div>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-admin.slug-display :slug="$category?->exists ? $category->slug : null" :url="$category?->exists && route('products.category', $category->slug) ? route('products.category', $category->slug) : null" source="English name" :follows="true" />
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Sort Order</label><input type="number" wire:model="form.sort_order" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Parent</label>
                <select wire:model="form.parent_id" class="w-full rounded-lg border-gray-300 text-sm"><option value="">— None —</option>@foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->getTranslation('name', app()->getLocale()) }}</option>@endforeach</select>
            </div>
            <div><label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_active"> Active</label></div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <label class="text-xs font-semibold text-gray-500 block mb-2">Category Image</label>
            <div class="flex items-center gap-4">
                @if($image)
                    <img src="{{ $image->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                @elseif($category?->exists && $category->hasMedia('image'))
                    <img src="{{ $category->getFirstMediaUrl('image') }}" class="w-20 h-20 object-cover rounded-lg border border-gray-200">
                @else
                    <div class="w-20 h-20 rounded-lg bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center text-gray-300 text-xs">No image</div>
                @endif
                <div class="flex-1">
                    <input type="file" wire:model="image" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand/10 file:text-brand hover:file:bg-brand/20 transition">
                    <div wire:loading wire:target="image" class="text-xs text-gray-400 mt-1">Uploading…</div>
                    @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($category?->exists && $category->hasMedia('image'))
                        <button type="button" wire:click="removeImage" wire:confirm="Remove this image?" class="text-xs text-red-500 hover:text-red-700 mt-1">Remove current image</button>
                    @endif
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex gap-2 mb-5 border-b border-gray-200">
                @foreach(['uz'=>"O'zbek",'ru'=>'Русский','en'=>'English'] as $code => $label)
                <button type="button" @click="locale='{{ $code }}'" :class="locale==='{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-400'" class="pb-2 px-3 text-sm transition">{{ $label }}</button>
                @endforeach
            </div>
            @foreach(['uz','ru','en'] as $code)
            <div x-show="locale==='{{ $code }}'">
                <div class="mb-3"><label class="text-xs font-semibold text-gray-500 block mb-1">Name ({{ $code }})@if($code === 'uz') <span class="text-red-400">*</span>@endif</label><input type="text" wire:model="form.name.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm">@error("form.name.{$code}") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                <div><label class="text-xs font-semibold text-gray-500 block mb-1">Description ({{ $code }})</label><textarea wire:model="form.description.{{ $code }}" rows="3" class="w-full rounded-lg border-gray-300 text-sm"></textarea></div>
            </div>
            @endforeach
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">Save</button>
            <a href="{{ route('admin.categories.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm hover:bg-gray-50 transition">Cancel</a>
        </div>
    </form>
</div>
