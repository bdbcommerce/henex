<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">Categories</h2>
        <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-1.5 bg-brand text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-brand-dark transition"><x-admin.icon name="plus" class="w-4 h-4" /> Add Category</a>
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
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded-full {{ $cat->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $cat->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.categories.edit', $cat) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                                <x-admin.icon name="edit" class="w-3.5 h-3.5" /> Edit
                            </a>
                            <button wire:click="delete({{ $cat->id }})" wire:confirm="Delete?"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition">
                                <x-admin.icon name="trash" class="w-3.5 h-3.5" /> Delete
                            </button>
                        </div>
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
