<div x-data="{ locale: 'uz' }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">{{ $category?->exists ? 'Edit Category' : 'New Category' }}</h2>
        <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-500 hover:text-brand">← Back</a>
    </div>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Slug</label><input type="text" wire:model="form.slug" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Sort Order</label><input type="number" wire:model="form.sort_order" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Parent</label>
                <select wire:model="form.parent_id" class="w-full rounded-lg border-gray-300 text-sm"><option value="">— None —</option>@foreach($parents as $p)<option value="{{ $p->id }}">{{ $p->getTranslation('name','en') }}</option>@endforeach</select>
            </div>
            <div><label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_active"> Active</label></div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex gap-2 mb-5 border-b border-gray-200">
                @foreach(['uz'=>"O'zbek",'ru'=>'Русский','en'=>'English'] as $code => $label)
                <button type="button" @click="locale='{{ $code }}'" :class="locale==='{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-400'" class="pb-2 px-3 text-sm transition">{{ $label }}</button>
                @endforeach
            </div>
            @foreach(['uz','ru','en'] as $code)
            <div x-show="locale==='{{ $code }}'">
                <div class="mb-3"><label class="text-xs font-semibold text-gray-500 block mb-1">Name ({{ $code }})</label><input type="text" wire:model="form.name.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm"></div>
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
