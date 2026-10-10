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
                <x-admin.slug-display :slug="$product?->exists ? $product->slug : null" :url="$product?->exists && route('products.show', $product->slug) ? route('products.show', $product->slug) : null" source="SKU" :follows="true" />
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
            @php
                $categoryOptions = $categories->flatMap(fn ($cat) => collect([
                    ['id' => $cat->id, 'name' => $cat->getTranslation('name', 'en'), 'child' => false],
                ])->concat($cat->children->map(fn ($child) => [
                    'id' => $child->id, 'name' => $child->getTranslation('name', 'en'), 'child' => true,
                ])))->values();
            @endphp
            <x-admin.multi-select :options="$categoryOptions" model="selectedCategories" placeholder="Select categories…" />
            @error('selectedCategories') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Attributes --}}
        @php
            $attrLabel = fn ($a) => $a->getTranslation('name', 'en') ?: $a->getTranslation('name', 'uz');
            $valueLabel = fn ($v) => $v->getTranslation('value', 'en') ?: $v->getTranslation('value', 'uz');
            $added = $productAttributes->filter(fn ($a) => array_key_exists($a->id, $selectedAttributes));
            $available = $productAttributes->reject(fn ($a) => array_key_exists($a->id, $selectedAttributes));
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="flex items-start justify-between gap-4 px-6 pt-5 pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                        Attributes
                        @if($added->isNotEmpty())<span class="px-1.5 py-0.5 rounded-md bg-gray-100 text-gray-600 text-[11px] font-semibold">{{ $added->count() }}</span>@endif
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Shown as the spec table on the product page and used by catalog filters and the Telegram advisor.</p>
                </div>
                <a href="{{ route('admin.attributes.index') }}" target="_blank" class="shrink-0 text-xs text-gray-500 hover:text-brand">Manage attributes ↗</a>
            </div>

            @if($productAttributes->isEmpty())
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500">No attributes exist yet.</p>
                    <a href="{{ route('admin.attributes.create') }}" target="_blank" class="inline-block mt-2 text-sm font-semibold text-brand hover:underline">Create the first attribute ↗</a>
                </div>
            @else
                {{-- Assigned attributes --}}
                @if($added->isNotEmpty())
                <ul class="divide-y divide-gray-100">
                    @foreach($added as $attr)
                    <li wire:key="attr-row-{{ $attr->id }}"
                        x-data="{ flash: false, get empty() { return ! (($wire.selectedAttributes ?? {})[{{ $attr->id }}] ?? []).length } }"
                        @attribute-added.window="if ($event.detail.id === {{ $attr->id }}) { $nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'center' })); flash = true; setTimeout(() => flash = false, 1800) }"
                        :class="flash ? 'bg-brand/5' : ''"
                        class="group grid grid-cols-1 md:grid-cols-[180px_1fr_auto] gap-x-4 gap-y-2 px-6 py-4 transition-colors duration-700">
                        <div class="pt-1">
                            <div class="text-sm font-medium text-gray-800">{{ $attrLabel($attr) }}</div>
                            <div class="text-[11px] text-gray-400">{{ $attr->is_multiple ? 'Select one or more' : 'Select one' }}</div>
                        </div>
                        <div>
                            @if($attr->values->count() <= 12)
                                <x-admin.value-pills
                                    :options="$attr->values->map(fn ($v) => ['id' => $v->id, 'name' => $valueLabel($v)])"
                                    :model="'selectedAttributes.' . $attr->id"
                                    :multiple="$attr->is_multiple"
                                    :label="$attrLabel($attr)" />
                            @else
                                <x-admin.multi-select
                                    :options="$attr->values->map(fn ($v) => ['id' => $v->id, 'name' => $valueLabel($v)])"
                                    :model="'selectedAttributes.' . $attr->id"
                                    :multiple="$attr->is_multiple"
                                    placeholder="Search and select…" />
                            @endif
                            <p x-show="empty" style="display:none" class="mt-1.5 text-[11px] text-amber-600">No value selected — this attribute will not be saved.</p>
                        </div>
                        <button type="button" wire:click="removeAttribute({{ $attr->id }})"
                                title="Remove {{ $attrLabel($attr) }} from this product" aria-label="Remove {{ $attrLabel($attr) }}"
                                class="justify-self-end self-start p-1.5 rounded-lg text-gray-300 hover:text-red-600 hover:bg-red-50 md:opacity-0 md:group-hover:opacity-100 focus:opacity-100 transition">
                            <x-admin.icon name="close" class="w-4 h-4" />
                        </button>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="px-6 py-6 text-center">
                    <p class="text-sm text-gray-500">This product has no attributes yet.</p>
                    <p class="text-xs text-gray-400 mt-0.5">Add the ones that describe it, e.g. scan engine, connectivity, form factor.</p>
                </div>
                @endif

                {{-- Add attribute: searchable popover --}}
                @if($available->isNotEmpty())
                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-3"
                     x-data="{
                        open: false, q: '',
                        items: @js($available->map(fn ($a) => ['id' => $a->id, 'name' => $attrLabel($a), 'count' => $a->values->count(), 'multiple' => $a->is_multiple])->values()),
                        get list() { const q = this.q.toLowerCase(); return q ? this.items.filter(i => i.name.toLowerCase().includes(q)) : this.items },
                        pick(id) { this.open = false; this.q = ''; $wire.addAttribute(id) },
                     }"
                     @keydown.escape="open = false">
                    <div class="relative" @click.outside="open = false">
                        <button type="button" @click="open = !open; $nextTick(() => open && $refs.q.focus())" :aria-expanded="open.toString()"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-dashed border-brand/60 px-3 py-2 text-sm font-semibold text-brand-dark hover:bg-brand/5 hover:border-brand transition">
                            <x-admin.icon name="plus" class="w-4 h-4" /> Add attribute
                        </button>
                        <div x-show="open" x-transition.origin.top.left style="display:none"
                             class="absolute left-0 z-30 mt-2 w-72 rounded-xl border border-gray-200 bg-white shadow-xl">
                            <div class="p-2 border-b border-gray-100">
                                <input x-ref="q" x-model="q" type="text" placeholder="Search attributes…"
                                       @keydown.enter.prevent="list.length && pick(list[0].id)"
                                       class="w-full rounded-lg border-gray-300 text-sm focus:border-brand focus:ring-brand">
                            </div>
                            <ul class="max-h-64 overflow-y-auto py-1">
                                <template x-for="item in list" :key="item.id">
                                    <li>
                                        <button type="button" @click="pick(item.id)" class="w-full flex items-center justify-between gap-3 px-3 py-2 text-left text-sm hover:bg-gray-50">
                                            <span class="font-medium text-gray-800" x-text="item.name"></span>
                                            <span class="text-[11px] text-gray-400 whitespace-nowrap" x-text="item.count + ' values · ' + (item.multiple ? 'multi' : 'single')"></span>
                                        </button>
                                    </li>
                                </template>
                                <li x-show="!list.length" class="px-3 py-3 text-sm text-gray-400">No match.</li>
                            </ul>
                        </div>
                    </div>
                    @if($available->count() > 1)
                        <button type="button" wire:click="addAllAttributes" class="text-xs text-gray-500 hover:text-brand">Add all {{ $available->count() }}</button>
                    @endif
                </div>
                @endif
            @endif
        </div>

        {{-- Images --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6" x-data="{ imgDel: null }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-700">Gallery Images</h3>
                <button type="button" wire:click="openMediaBrowser"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                    <x-admin.icon name="folder" class="w-3.5 h-3.5" /> Browse Library
                </button>
            </div>

            @if(count($existingImages))
            <div class="grid grid-cols-3 sm:grid-cols-5 gap-3 mb-4">
                @foreach($existingImages as $img)
                <div class="relative group">
                    <img src="{{ $img['url'] }}" alt="{{ $img['name'] }}"
                         class="w-full aspect-square object-cover rounded-lg border border-gray-200">
                    <button type="button" @click="imgDel = @js(['id' => $img['id'], 'url' => $img['url'], 'name' => $img['name']])"
                            class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition leading-none">
                        &times;
                    </button>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Delete image: keep the file on the server, or delete it too --}}
            <div x-show="imgDel" x-transition.opacity class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display: none" @keydown.escape.window="imgDel = null">
                <div class="bg-white rounded-xl shadow-lg max-w-md w-full" @click.outside="imgDel = null">
                    <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-neutral-950">Remove image?</h3></div>
                    <div class="px-6 py-4 flex gap-4">
                        <img :src="imgDel?.url" class="w-20 h-20 object-cover rounded-lg border border-gray-200 shrink-0">
                        <div class="text-sm text-gray-600 space-y-2">
                            <p>The image will be removed from this product.</p>
                            <p><b>Also delete the file from the server?</b> If you keep it, it stays under “Unused files on server” in Browse Library and can be added to any product later.</p>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 flex flex-wrap gap-2 justify-end">
                        <button type="button" @click="imgDel = null" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 transition font-medium text-sm">Cancel</button>
                        <button type="button" @click="$wire.deleteImage(imgDel.id, false); imgDel = null"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition font-medium text-sm">Remove, keep file</button>
                        <button type="button" @click="$wire.deleteImage(imgDel.id, true); imgDel = null"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm">Delete from server too</button>
                    </div>
                </div>
            </div>

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

            @if(count($pickedMedia) || count($pickedFiles))
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-500 mb-2">From library — will save on submit</p>
                <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                    @foreach($pickedMedia as $img)
                    <div class="relative group" wire:key="pm-{{ $img['id'] }}">
                        <img src="{{ $img['url'] }}" alt="{{ $img['name'] }}"
                             class="w-full aspect-square object-cover rounded-lg border border-brand/40">
                        <button type="button" wire:click="removePicked({{ $img['id'] }})"
                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition leading-none">
                            &times;
                        </button>
                    </div>
                    @endforeach
                    @foreach($pickedFiles as $key => $file)
                    <div class="relative group" wire:key="pf-{{ $key }}">
                        <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}"
                             class="w-full aspect-square object-cover rounded-lg border border-dashed border-brand">
                        <button type="button" wire:click="removePickedFile('{{ $key }}')"
                                class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition leading-none">
                            &times;
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Media Library Browser Modal --}}
        @if($showMediaBrowser)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click.self="closeMediaBrowser">
            <div class="bg-white rounded-xl shadow-lg max-w-3xl w-full max-h-[85vh] flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        @if($browsingProductId || $browsingUnused)
                        <button type="button" wire:click="browseFolder(null)" class="text-gray-400 hover:text-gray-700 transition">
                            <x-admin.icon name="arrow-left" class="w-4 h-4" />
                        </button>
                        @endif
                        <h3 class="text-sm font-semibold text-gray-700">
                            {{ $browsingUnused ? 'Unused files on server' : ($browsingProductId ? 'Select images to reuse' : 'Media Library — browse by product') }}
                        </h3>
                    </div>
                    <button type="button" wire:click="closeMediaBrowser" class="text-gray-400 hover:text-gray-700 transition">
                        <x-admin.icon name="close" class="w-4 h-4" />
                    </button>
                </div>

                <div class="px-6 py-4 overflow-y-auto">
                    @if($browsingUnused)
                        {{-- Files kept on the server after being removed from a product --}}
                        @if(count($unusedFiles))
                        <p class="text-xs text-gray-500 mb-3">Click to select for this product. 🗑 deletes the file from the server permanently.</p>
                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                            @foreach($unusedFiles as $file)
                            @php $picked = isset($pickedFiles[$file['key']]); @endphp
                            <div class="relative group" wire:key="uf-{{ $file['key'] }}">
                                <button type="button" wire:click="togglePickFile(@js($file['path']))" class="block w-full">
                                    <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" loading="lazy"
                                         class="w-full aspect-square object-cover rounded-lg border-2 transition {{ $picked ? 'border-brand' : 'border-gray-200' }}">
                                    @if($picked)
                                    <div class="absolute inset-0 bg-brand/20 rounded-lg flex items-center justify-center">
                                        <span class="bg-brand text-white rounded-full w-6 h-6 flex items-center justify-center"><x-admin.icon name="check-circle" class="w-4 h-4" /></span>
                                    </div>
                                    @endif
                                </button>
                                <p class="text-[10px] text-gray-400 truncate mt-1">{{ $file['name'] }}</p>
                                <button type="button" wire:click="deleteUnusedFile(@js($file['path']))"
                                        wire:confirm="Delete {{ $file['name'] }} from the server permanently?"
                                        class="absolute top-1 right-1 bg-white/90 text-red-600 rounded-full w-6 h-6 text-xs flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow">🗑</button>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400 text-center py-10">No unused files — every file on the server belongs to a product.</p>
                        @endif
                    @elseif(!$browsingProductId)
                        {{-- Folder view: one folder per product that already has images --}}
                        @if(count($unusedFiles))
                        <button type="button" wire:click="browseUnused" class="w-full mb-4 flex items-center gap-3 p-3 rounded-lg border border-dashed border-gray-300 hover:border-brand text-left transition">
                            <x-admin.icon name="folder" class="w-6 h-6 text-amber-500" />
                            <span class="text-sm font-medium text-gray-700">Unused files on server</span>
                            <span class="text-xs text-gray-400">{{ count($unusedFiles) }} file{{ count($unusedFiles) === 1 ? '' : 's' }} not attached to any product</span>
                        </button>
                        @endif
                        @if(count($mediaFolders))
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                            @foreach($mediaFolders as $folder)
                            <button type="button" wire:click="browseFolder({{ $folder['id'] }})"
                                    class="text-left group">
                                <div class="aspect-square rounded-lg border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center group-hover:border-brand transition">
                                    @if($folder['cover'])
                                        <img src="{{ $folder['cover'] }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <x-admin.icon name="folder" class="w-8 h-8 text-gray-300" />
                                    @endif
                                </div>
                                <p class="text-xs font-medium text-gray-700 mt-1.5 truncate">{{ $folder['name'] }}</p>
                                <p class="text-[11px] text-gray-400">{{ $folder['count'] }} image{{ $folder['count'] === 1 ? '' : 's' }}</p>
                            </button>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400 text-center py-10">No other products have uploaded images yet.</p>
                        @endif
                    @else
                        {{-- Folder contents: pick images to reuse on this product --}}
                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-3">
                            @foreach($folderMedia as $img)
                            @php $picked = isset($pickedMedia[$img['id']]); @endphp
                            <button type="button" wire:click="togglePick({{ $img['id'] }})" class="relative">
                                <img src="{{ $img['url'] }}" alt="{{ $img['name'] }}"
                                     class="w-full aspect-square object-cover rounded-lg border-2 transition {{ $picked ? 'border-brand' : 'border-gray-200' }}">
                                @if($picked)
                                <div class="absolute inset-0 bg-brand/20 rounded-lg flex items-center justify-center">
                                    <span class="bg-brand text-white rounded-full w-6 h-6 flex items-center justify-center">
                                        <x-admin.icon name="check-circle" class="w-4 h-4" />
                                    </span>
                                </div>
                                @endif
                            </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between shrink-0">
                    @php $pickedCount = count($pickedMedia) + count($pickedFiles); @endphp
                    <p class="text-xs text-gray-500">{{ $pickedCount }} image{{ $pickedCount === 1 ? '' : 's' }} selected</p>
                    <button type="button" wire:click="closeMediaBrowser"
                            class="px-4 py-2 bg-brand text-white rounded-lg text-sm font-medium hover:bg-brand-dark transition">
                        Done
                    </button>
                </div>
            </div>
        </div>
        @endif

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
