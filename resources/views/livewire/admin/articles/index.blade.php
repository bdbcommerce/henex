<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">Articles</h2>
        <a href="{{ route('admin.articles.create') }}" class="bg-brand text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-brand-dark transition">+ New Article</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-100"><input wire:model.live.debounce.300ms="search" type="text" placeholder="Search…" class="w-72 rounded-lg border-gray-300 text-sm shadow-sm"></div>
        <table class="w-full text-sm"><thead class="bg-gray-50 text-gray-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left">Title</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Published</th><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Actions</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($articles as $a)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $a->getTranslation('title','en') }}</td>
                <td class="px-4 py-3"><span class="bg-brand/10 text-brand text-xs px-2 py-0.5 rounded-full">{{ $a->type }}</span></td>
                <td class="px-4 py-3">{{ $a->is_published ? '✅' : '⏸' }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $a->published_at?->format('d.m.Y') }}</td>
                <td class="px-4 py-3 flex gap-2"><a href="{{ route('admin.articles.edit', $a) }}" class="text-brand hover:underline">Edit</a><button wire:click="delete({{ $a->id }})" wire:confirm="Delete?" class="text-red-500 hover:underline">Delete</button></td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No articles.</td></tr>
            @endforelse
        </tbody></table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $articles->links() }}</div>
    </div>
</div>
