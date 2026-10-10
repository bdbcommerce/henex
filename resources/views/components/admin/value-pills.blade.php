{{--
    One-click value picker: every option visible as a toggle pill.
    options:  [['id' => int, 'name' => string], ...]
    model:    Livewire property path holding an array of selected ids
    multiple: false → behaves like a radio group (click again to clear)
--}}
@props(['options', 'model', 'multiple' => true, 'label' => ''])

<div x-data="{
        multiple: @js((bool) $multiple),
        selected: $wire.entangle(@js($model)),
        ids() { return (this.selected ?? []).map(Number) },
        on(id) { return this.ids().includes(id) },
        toggle(id) {
            if (this.multiple) {
                this.selected = this.on(id) ? this.ids().filter(x => x !== id) : [...this.ids(), id];
            } else {
                this.selected = this.on(id) ? [] : [id];
            }
        },
     }"
     role="{{ $multiple ? 'group' : 'radiogroup' }}" aria-label="{{ $label }}"
     {{ $attributes->merge(['class' => 'flex flex-wrap gap-1.5']) }}>
    @foreach($options as $opt)
        <button type="button" @click="toggle({{ $opt['id'] }})"
                role="{{ $multiple ? 'checkbox' : 'radio' }}"
                :aria-checked="on({{ $opt['id'] }}).toString()"
                :class="on({{ $opt['id'] }})
                    ? 'bg-brand border-brand text-white shadow-sm'
                    : 'bg-white border-gray-300 text-gray-700 hover:border-brand hover:text-brand-dark'"
                class="inline-flex items-center gap-1 rounded-full border px-3 py-1 text-xs font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand/50">
            <svg x-show="on({{ $opt['id'] }})" class="w-3 h-3 -ml-0.5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M16.7 5.3a1 1 0 010 1.4l-7.5 7.5a1 1 0 01-1.4 0L3.3 9.7a1 1 0 011.4-1.4l3.8 3.8 6.8-6.8a1 1 0 011.4 0z" clip-rule="evenodd"/>
            </svg>
            {{ $opt['name'] }}
        </button>
    @endforeach
</div>
