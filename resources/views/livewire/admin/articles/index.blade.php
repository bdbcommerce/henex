<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">Articles</h2>
        <a href="{{ route('admin.articles.create') }}" class="inline-flex items-center gap-1.5 bg-brand text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-brand-dark transition"><x-admin.icon name="plus" class="w-4 h-4" /> New Article</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-100"><input wire:model.live.debounce.300ms="search" type="text" placeholder="Search…" class="w-72 rounded-lg border-gray-300 text-sm shadow-sm"></div>
        <table class="w-full text-sm"><thead class="bg-gray-50 text-gray-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left">Title</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Published</th><th class="px-4 py-3 text-left">Date</th><th class="px-4 py-3 text-left">Actions</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($articles as $a)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $a->getTranslation('title','en') }}</td>
                <td class="px-4 py-3"><span class="bg-brand/10 text-brand text-xs px-2 py-0.5 rounded-full">{{ $a->type }}</span></td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full {{ $a->is_published ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $a->is_published ? 'Published' : 'Draft' }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500">{{ $a->published_at?->format('d.m.Y') }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.articles.edit', $a) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                            <x-admin.icon name="edit" class="w-3.5 h-3.5" /> Edit
                        </a>
                        <button wire:click="delete({{ $a->id }})" wire:confirm="Delete?"
                                class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition">
                            <x-admin.icon name="trash" class="w-3.5 h-3.5" /> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No articles.</td></tr>
            @endforelse
        </tbody></table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $articles->links() }}</div>
    </div>
</div>
