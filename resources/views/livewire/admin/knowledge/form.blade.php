<div x-data="{ locale: 'uz' }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">{{ $article?->exists ? 'Edit Article' : 'New Article' }}</h2>
        <a href="{{ route('admin.knowledge.index') }}" class="text-sm text-gray-500 hover:text-brand">← Back</a>
    </div>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-admin.slug-display :slug="$article?->exists ? $article->slug : null" source="title" :follows="false" />
            <div>
                <label class="text-xs font-semibold text-gray-500 block mb-1">Sort Order</label>
                <input type="number" wire:model="form.sort_order" class="w-full rounded-lg border-gray-300 text-sm">
            </div>
            <div class="pt-6"><label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_active" class="rounded border-gray-300 text-brand focus:ring-brand"> Active (bot can use it)</label></div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex gap-2 mb-5 border-b border-gray-200">
                @foreach(['uz'=>"O'zbek",'ru'=>'Русский','en'=>'English'] as $code => $label)
                <button type="button" @click="locale='{{ $code }}'" :class="locale==='{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-400'" class="pb-2 px-3 text-sm transition">{{ $label }}</button>
                @endforeach
            </div>
            @foreach(['uz','ru','en'] as $code)
            <div x-show="locale==='{{ $code }}'" class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Title / question ({{ $code }})@if($code === 'uz') <span class="text-red-400">*</span>@endif</label>
                    <input type="text" wire:model="form.title.{{ $code }}" placeholder="{{ ['uz' => "Kod oxiriga Enter qo'shish", 'ru' => 'Как добавить Enter после кода', 'en' => 'How to add Enter after the code'][$code] }}" class="w-full rounded-lg border-gray-300 text-sm">
                    @error("form.title.{$code}") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Keywords ({{ $code }}) — comma-separated words clients use</label>
                    <input type="text" wire:model="form.keywords.{{ $code }}" placeholder="{{ ['uz' => 'enter, suffiks, yangi qator', 'ru' => 'enter, суффикс, перевод строки, CR', 'en' => 'enter, suffix, carriage return, CR'][$code] }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-500 block mb-1">Answer ({{ $code }}) — steps the client should follow</label>
                    <textarea wire:model="form.answer.{{ $code }}" rows="8" class="w-full rounded-lg border-gray-300 text-sm font-mono"></textarea>
                    <p class="text-xs text-gray-400 mt-1">Plain text or simple HTML (&lt;p&gt;, &lt;ul&gt;&lt;li&gt;, &lt;b&gt;). The bot sends it as text.</p>
                </div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Setting barcodes --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">Setting barcodes</h3>
                <p class="text-xs text-gray-500 mb-4">Images of configuration barcodes. The bot sends them as photos for the client to scan, in this order. The file name becomes the caption.</p>
                @if($article?->exists && ($barcodes = $article->getMedia('barcodes'))->isNotEmpty())
                <div class="grid grid-cols-3 gap-3 mb-4">
                    @foreach($barcodes as $m)
                    <div class="relative group border border-gray-200 rounded-lg p-1" wire:key="bc-{{ $m->id }}">
                        <img src="{{ $m->getUrl() }}" class="w-full aspect-square object-contain">
                        <div class="text-[10px] text-gray-500 truncate mt-1">{{ $m->name }}</div>
                        <button type="button" wire:click="deleteMedia({{ $m->id }})" class="absolute top-1 right-1 bg-red-600 text-white rounded-full w-5 h-5 text-xs opacity-0 group-hover:opacity-100 transition">&times;</button>
                    </div>
                    @endforeach
                </div>
                @endif
                <input type="file" wire:model="newBarcodes" multiple accept="image/*"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand/10 file:text-brand hover:file:bg-brand/20">
                <div wire:loading wire:target="newBarcodes" class="text-xs text-gray-400 mt-1">Uploading…</div>
                @error('newBarcodes.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                @if($newBarcodes)
                    <div class="flex flex-wrap gap-2 mt-3">
                        @foreach($newBarcodes as $file)
                            <img src="{{ $file->temporaryUrl() }}" class="w-16 h-16 object-contain border border-dashed border-brand rounded">
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Manuals --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-1">PDF manuals</h3>
                <p class="text-xs text-gray-500 mb-4">Sent as documents when the client opens this article in the bot.</p>
                @if($article?->exists && ($manuals = $article->getMedia('manuals'))->isNotEmpty())
                <ul class="divide-y divide-gray-100 mb-4 text-sm">
                    @foreach($manuals as $m)
                    <li class="flex items-center justify-between py-2" wire:key="pdf-{{ $m->id }}">
                        <a href="{{ $m->getUrl() }}" target="_blank" class="text-blue-700 hover:underline truncate">📄 {{ $m->name }}</a>
                        <span class="flex items-center gap-3 shrink-0">
                            <span class="text-xs text-gray-400">{{ $m->human_readable_size }}</span>
                            <button type="button" wire:click="deleteMedia({{ $m->id }})" class="text-red-500 hover:text-red-700 text-xs">Remove</button>
                        </span>
                    </li>
                    @endforeach
                </ul>
                @endif
                <input type="file" wire:model="newManuals" multiple accept="application/pdf"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand/10 file:text-brand hover:file:bg-brand/20">
                <div wire:loading wire:target="newManuals" class="text-xs text-gray-400 mt-1">Uploading…</div>
                @error('newManuals.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">Save</button>
            <a href="{{ route('admin.knowledge.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm hover:bg-gray-50 transition">Cancel</a>
        </div>
    </form>
</div>
