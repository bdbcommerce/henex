<div>
    <h2 class="text-xl font-bold text-neutral-950 mb-6">Hero Slides</h2>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm divide-y divide-gray-100">
        @forelse($slides as $slide)
        <div class="flex items-center gap-4 px-5 py-4">
            <div class="flex-1">
                <p class="font-semibold text-sm">{{ $slide->getTranslation('title','en') }}</p>
                <p class="text-xs text-gray-400">{{ $slide->getTranslation('subtitle','en') }}</p>
            </div>
            <span class="{{ $slide->is_active ? 'text-green-600' : 'text-gray-400' }} text-xs font-semibold">{{ $slide->is_active ? 'Active' : 'Hidden' }}</span>
            <button wire:click="toggleActive({{ $slide->id }})" class="text-xs px-3 py-1 rounded-lg border border-gray-300 hover:bg-gray-50 transition">Toggle</button>
            <button wire:click="delete({{ $slide->id }})" wire:confirm="Delete slide?" class="text-xs text-red-500 hover:underline">Delete</button>
        </div>
        @empty
        <div class="px-5 py-10 text-center text-gray-400 text-sm">No slides yet. Add slides via the database seeder or future upload form.</div>
        @endforelse
    </div>
</div>
