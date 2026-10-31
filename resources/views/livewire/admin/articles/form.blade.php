<div x-data="{ locale: 'uz' }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">{{ $article?->exists ? 'Edit Article' : 'New Article' }}</h2>
        <a href="{{ route('admin.articles.index') }}" class="text-sm text-gray-500 hover:text-brand">← Back</a>
    </div>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-admin.slug-display :slug="$article?->exists ? $article->slug : null" :url="$article?->exists && route('news.show', $article->slug) ? route('news.show', $article->slug) : null" source="title" :follows="true" />
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Type</label><select wire:model="form.type" class="w-full rounded-lg border-gray-300 text-sm"><option value="news">News</option><option value="blog">Blog</option><option value="guide">Guide</option></select></div>
            <div><label class="text-xs font-semibold text-gray-500 block mb-1">Published At</label><input type="datetime-local" wire:model="form.published_at" class="w-full rounded-lg border-gray-300 text-sm"></div>
            <div><label class="flex items-center gap-2 text-sm"><input type="checkbox" wire:model="form.is_published"> Published</label></div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <label class="text-xs font-semibold text-gray-500 block mb-2">Cover Image</label>
            <div class="flex items-center gap-4">
                @if($cover)
                    <img src="{{ $cover->temporaryUrl() }}" class="w-28 h-16 object-cover rounded-lg border border-gray-200">
                @elseif($article?->exists && $article->hasMedia('cover'))
                    <img src="{{ $article->getFirstMediaUrl('cover') }}" class="w-28 h-16 object-cover rounded-lg border border-gray-200">
                @else
                    <div class="w-28 h-16 rounded-lg bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center text-gray-300 text-xs">No image</div>
                @endif
                <div class="flex-1">
                    <input type="file" wire:model="cover" accept="image/*"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand/10 file:text-brand hover:file:bg-brand/20 transition">
                    <div wire:loading wire:target="cover" class="text-xs text-gray-400 mt-1">Uploading…</div>
                    @error('cover') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($article?->exists && $article->hasMedia('cover'))
                        <button type="button" wire:click="removeCover" wire:confirm="Remove this cover image?" class="text-xs text-red-500 hover:text-red-700 mt-1">Remove current image</button>
                    @endif
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex gap-2 mb-5 border-b border-gray-200">@foreach(['uz'=>"O'zbek",'ru'=>'Русский','en'=>'English'] as $code => $label)<button type="button" @click="locale='{{ $code }}'" :class="locale==='{{ $code }}' ? 'border-b-2 border-brand text-brand font-semibold' : 'text-gray-400'" class="pb-2 px-3 text-sm transition">{{ $label }}</button>@endforeach</div>
            @foreach(['uz','ru','en'] as $code)
            <div x-show="locale==='{{ $code }}'">
                <div class="mb-3"><label class="text-xs font-semibold text-gray-500 block mb-1">Title ({{ $code }})@if($code === 'uz') <span class="text-red-400">*</span>@endif</label><input type="text" wire:model="form.title.{{ $code }}" class="w-full rounded-lg border-gray-300 text-sm">@error("form.title.{$code}") <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                <div class="mb-3"><label class="text-xs font-semibold text-gray-500 block mb-1">Excerpt ({{ $code }})</label><textarea wire:model="form.excerpt.{{ $code }}" rows="2" class="w-full rounded-lg border-gray-300 text-sm"></textarea></div>
                <div><label class="text-xs font-semibold text-gray-500 block mb-1">Content ({{ $code }})</label><textarea wire:model="form.content.{{ $code }}" rows="10" class="w-full rounded-lg border-gray-300 text-sm font-mono"></textarea></div>
            </div>
            @endforeach
        </div>
        <div class="flex gap-3">
            <button type="submit" class="bg-brand text-white font-semibold px-6 py-2.5 rounded-lg hover:bg-brand-dark transition text-sm">Save</button>
            <a href="{{ route('admin.articles.index') }}" class="px-6 py-2.5 rounded-lg border border-gray-300 text-sm hover:bg-gray-50 transition">Cancel</a>
        </div>
    </form>
</div>
