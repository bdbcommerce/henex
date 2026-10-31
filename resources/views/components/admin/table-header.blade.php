<div class="mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-2xl font-bold text-neutral-950">{{ $title }}</h2>
            @if($description ?? false)
                <p class="text-gray-600 text-sm mt-1">{{ $description }}</p>
            @endif
        </div>
        @if($createRoute ?? false)
            <a href="{{ $createRoute }}" class="bg-brand hover:bg-brand-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                + Add New
            </a>
        @endif
    </div>
    @if($search ?? false)
        <div class="flex gap-3 items-center">
            <input type="text"
                   placeholder="Search..."
                   wire:model.live.debounce.300ms="search"
                   class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand text-sm">
            @if($filters ?? false)
                <select wire:model.live="filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand text-sm">
                    {{ $filters }}
                </select>
            @endif
        </div>
    @endif
</div>
