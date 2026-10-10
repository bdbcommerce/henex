{{--
    Searchable dropdown bound to a Livewire array property.
    options: [['id' => int, 'name' => string, 'child' => bool?], ...]
    model:   Livewire property path holding an array of selected ids, e.g. "selectedCategories" or "selectedAttributes.3"
    multiple: false → picking a value replaces the selection and closes the list
--}}
@props(['options', 'model', 'multiple' => true, 'placeholder' => 'Select…'])

<div x-data="{
        open: false,
        query: '',
        multiple: @js((bool) $multiple),
        options: @js(collect($options)->values()),
        selected: $wire.entangle(@js($model)),
        ids() { return (this.selected ?? []).map(Number) },
        isSelected(id) { return this.ids().includes(id) },
        toggle(id) {
            if (! this.multiple) {
                this.selected = this.isSelected(id) ? [] : [id];
                this.open = false;
                return;
            }
            this.selected = this.isSelected(id)
                ? this.ids().filter(s => s !== id)
                : [...this.ids(), id];
        },
        get filtered() {
            const q = this.query.toLowerCase();
            return q ? this.options.filter(o => o.name.toLowerCase().includes(q)) : this.options;
        },
        nameOf(id) { return this.options.find(o => o.id === Number(id))?.name ?? id },
     }"
     @click.outside="open = false" @keydown.escape="open = false"
     {{ $attributes->merge(['class' => 'relative']) }}>
    <button type="button" @click="open = !open"
            class="w-full min-h-[42px] flex flex-wrap items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-2 text-left text-sm focus:border-brand focus:ring-1 focus:ring-brand">
        <template x-for="id in (selected ?? [])" :key="id">
            <span class="inline-flex items-center gap-1 rounded-md bg-brand/10 px-2 py-0.5 text-xs font-medium text-brand-dark">
                <span x-text="nameOf(id)"></span>
                <span role="button" @click.stop="toggle(Number(id))" class="hover:text-red-500">&times;</span>
            </span>
        </template>
        <span x-show="!(selected ?? []).length" class="text-gray-400">{{ $placeholder }}</span>
        <svg class="ml-auto w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div x-show="open" x-transition.opacity style="display: none"
         class="absolute z-20 mt-1 w-full rounded-lg border border-gray-200 bg-white shadow-lg">
        <div class="p-2 border-b border-gray-100" x-show="options.length > 6">
            <input type="text" x-model="query" placeholder="Search…"
                   class="w-full rounded-md border-gray-300 text-sm focus:border-brand focus:ring-brand">
        </div>
        <ul class="max-h-60 overflow-y-auto py-1">
            <template x-for="opt in filtered" :key="opt.id">
                <li @click="toggle(opt.id)"
                    :class="opt.child ? 'pl-8 text-gray-600' : 'pl-3 text-gray-800'"
                    class="flex items-center gap-2 pr-3 py-2 text-sm cursor-pointer hover:bg-gray-50">
                    <input :type="multiple ? 'checkbox' : 'radio'" :checked="isSelected(opt.id)" tabindex="-1"
                           class="pointer-events-none border-gray-300 text-brand focus:ring-brand"
                           :class="multiple ? 'rounded' : 'rounded-full'">
                    <span x-text="(opt.child ? '↳ ' : '') + opt.name"></span>
                </li>
            </template>
            <li x-show="!filtered.length" class="px-3 py-2 text-sm text-gray-400">Nothing found</li>
        </ul>
    </div>
</div>
