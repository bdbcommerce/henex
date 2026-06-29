<div>
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold text-neutral-950">Resellers</h2>
        <a href="{{ route('admin.resellers.create') }}" class="bg-brand text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-brand-dark transition">+ Add Reseller</a>
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
                <td class="px-4 py-3 flex gap-2"><a href="{{ route('admin.resellers.edit', $r) }}" class="text-brand hover:underline">Edit</a><button wire:click="delete({{ $r->id }})" wire:confirm="Delete?" class="text-red-500 hover:underline">Delete</button></td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">No resellers.</td></tr>
            @endforelse
        </tbody></table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $resellers->links() }}</div>
    </div>
</div>
