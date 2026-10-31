<div x-data="{ deleteId: null }">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">Resellers</h2>
        <a href="{{ route('admin.resellers.create') }}" class="inline-flex items-center gap-1.5 bg-brand text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-brand-dark transition"><x-admin.icon name="plus" class="w-4 h-4" /> Add Reseller</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-100"><input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name…" class="w-72 rounded-lg border-gray-300 text-sm shadow-sm"></div>
        <table class="w-full text-sm"><thead class="bg-gray-50 text-gray-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Region</th><th class="px-4 py-3 text-left">Type</th><th class="px-4 py-3 text-left">Phone</th><th class="px-4 py-3 text-left">Actions</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($resellers as $r)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $r->name }}</td>
                <td class="px-4 py-3">{{ $r->region?->getTranslation('name','en') }}</td>
                <td class="px-4 py-3"><span class="bg-gray-100 text-gray-700 text-xs px-2 py-0.5 rounded-full">{{ $r->type }}</span></td>
                <td class="px-4 py-3 text-gray-500">{{ $r->phone }}</td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.resellers.edit', $r) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
                            <x-admin.icon name="edit" class="w-3.5 h-3.5" /> Edit
                        </a>
                        <button type="button" @click="deleteId = {{ $r->id }}"
                                class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition">
                            <x-admin.icon name="trash" class="w-3.5 h-3.5" /> Delete
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No resellers.</td></tr>
            @endforelse
        </tbody></table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $resellers->links() }}</div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteId" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display: none">
        <div class="bg-white rounded-xl shadow-lg max-w-sm w-full" @click.stop>
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-neutral-950">Delete Reseller?</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600">This will permanently delete the reseller and cannot be undone.</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                <button type="button" @click="deleteId = null"
                        class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 transition font-medium">
                    Cancel
                </button>
                <button type="button" @click="wire.call('delete', deleteId); deleteId = null"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>
