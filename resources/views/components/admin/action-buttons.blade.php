<div class="flex gap-2 justify-end">
    @if($editRoute ?? false)
        <a href="{{ $editRoute }}"
           class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
            ✏️ Edit
        </a>
    @endif
    @if($deleteAction ?? false)
        <button type="button"
                @click="confirmDelete = {{ $itemId }}"
                class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">
            🗑️ Delete
        </button>
    @endif
    @if($viewRoute ?? false)
        <a href="{{ $viewRoute }}"
           target="_blank"
           class="inline-flex items-center gap-1 px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
            👁️ View
        </a>
    @endif
</div>
