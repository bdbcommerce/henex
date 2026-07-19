<div x-data="{ locale: 'uz' }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">Hero Slides</h2>
        @if(!$showForm)
        <button wire:click="openCreate"
                class="bg-brand text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-brand-dark transition">
            + New Slide
        </button>
        @endif
    </div>

    {{-- Create / Edit Form --}}
    @if($showForm)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-semibold text-gray-700">{{ $editingId ? 'Edit Slide' : 'New Slide' }}</h3>
            <button wire:click="closeForm" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
        </div>

        <form wire:submit="save" class="space-y-5">

            {{-- Locale tabs --}}
            <div>
                <div class="flex gap-1 mb-4 border-b border-gray-200">
                    @foreach(['uz' => "O'zbek", 'ru' => 'Русский', 'en' => 'English'] as $code => $label)
                    <button type="button" @click="locale = '{{ $code }}'"
                            :class="locale === '{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-400 hover:text-gray-600'"
                            class="pb-2 px-4 text-sm transition">{{ $label }}</button>
                    @endforeach
                </div>

                @foreach(['uz', 'ru', 'en'] as $code)
                <div x-show="locale === '{{ $code }}'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">
                            Title ({{ strtoupper($code) }})@if($code === 'uz') <span class="text-red-400">*</span>@endif
                        </label>
                        <input type="text" wire:model="form.title.{{ $code }}"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                        @error("form.title.{$code}") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Subtitle ({{ strtoupper($code) }})</label>
                        <input type="text" wire:model="form.subtitle.{{ $code }}"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Button Text ({{ strtoupper($code) }})</label>
                        <input type="text" wire:model="form.button_text.{{ $code }}"
                               placeholder="e.g. View Products"
                               class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Link + order + active --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-gray-100">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Link URL (optional)</label>
                    <input type="url" wire:model="form.link" placeholder="https://..."
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1">Sort Order</label>
                    <input type="number" wire:model="form.sort_order"
                           class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                </div>
            </div>

            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" wire:model="form.is_active"
                       class="rounded border-gray-300 text-brand focus:ring-brand">
                <span>Active (visible on homepage)</span>
            </label>

            {{-- Image upload --}}
            <div class="pt-2 border-t border-gray-100">
                <label class="block text-xs font-semibold text-gray-500 mb-1">
                    Slide Image{{ $editingId ? ' (leave blank to keep current)' : '' }} <span class="text-red-400">*</span>
                </label>
                <input type="file" wire:model="image" accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand/10 file:text-brand hover:file:bg-brand/20 transition">
                <div wire:loading wire:target="image" class="text-xs text-gray-400 mt-1">Uploading…</div>
                @error('image') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @if($image)
                <p class="text-xs text-green-600 mt-1">{{ $image->getClientOriginalName() }} selected.</p>
                @endif
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait"
                        class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">
                    <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Slide' : 'Create Slide' }}</span>
                    <span wire:loading wire:target="save">Saving…</span>
                </button>
                <button type="button" wire:click="closeForm"
                        class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-50 transition">
                    Cancel
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Slide list --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm divide-y divide-gray-100">
        @forelse($slides as $slide)
        <div class="flex items-center gap-4 px-5 py-4">

            {{-- Thumbnail --}}
            @if($slide->hasMedia('slide'))
            <img src="{{ $slide->getFirstMediaUrl('slide') }}" alt=""
                 class="w-16 h-10 object-cover rounded-lg border border-gray-200 shrink-0">
            @else
            <div class="w-16 h-10 rounded-lg bg-gray-100 border border-dashed border-gray-300 shrink-0 flex items-center justify-center text-gray-300 text-xs">img</div>
            @endif

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm truncate">{{ $slide->getTranslation('title', 'uz') ?: $slide->getTranslation('title', 'en') }}</p>
                <p class="text-xs text-gray-400 truncate">{{ $slide->getTranslation('subtitle', 'uz') }}</p>
            </div>

            {{-- Sort --}}
            <div class="flex flex-col gap-0.5">
                <button wire:click="moveUp({{ $slide->id }})"
                        class="text-gray-400 hover:text-gray-700 p-1 leading-none" title="Move up">▲</button>
                <button wire:click="moveDown({{ $slide->id }})"
                        class="text-gray-400 hover:text-gray-700 p-1 leading-none" title="Move down">▼</button>
            </div>

            {{-- Status --}}
            <span class="{{ $slide->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-400' }} text-xs font-semibold px-2 py-0.5 rounded-full">
                {{ $slide->is_active ? 'Active' : 'Hidden' }}
            </span>

            {{-- Actions --}}
            <button wire:click="toggleActive({{ $slide->id }})"
                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                {{ $slide->is_active ? 'Hide' : 'Show' }}
            </button>
            <button wire:click="openEdit({{ $slide->id }})"
                    class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 hover:bg-gray-50 transition">
                Edit
            </button>
            <button wire:click="delete({{ $slide->id }})" wire:confirm="Delete this slide?"
                    class="text-xs text-red-500 hover:text-red-700 px-2 transition">
                Delete
            </button>
        </div>
        @empty
        <div class="px-5 py-12 text-center text-gray-400 text-sm">
            No slides yet. Click <span class="font-semibold text-gray-600">"+ New Slide"</span> to add one.
        </div>
        @endforelse
    </div>
</div>
