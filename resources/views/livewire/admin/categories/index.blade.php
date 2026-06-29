<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">Categories</h2>
        <a href="{{ route('admin.categories.create') }}" class="bg-brand text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-brand-dark transition">+ Add Category</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-100">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search…" class="w-72 rounded-lg border-gray-300 text-sm shadow-sm">
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Slug</th><th class="px-4 py-3 text-left">Active</th><th class="px-4 py-3 text-left">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold">{{ $cat->getTranslation('name','en') }}</td>
                    <td class="px-4 py-3 font-mono text-gray-500">{{ $cat->slug }}</td>
                    <td class="px-4 py-3">{{ $cat->is_active ? '✅' : '❌' }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        <a href="{{ route('admin.categories.edit', $cat) }}" class="text-brand hover:underline">Edit</a>
                        <button wire:click="delete({{ $cat->id }})" wire:confirm="Delete?" class="text-red-500 hover:underline">Delete</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No categories.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $categories->links() }}</div>
    </div>
</div>
