<div class="flex gap-2 justify-end">
    @if($editRoute ?? false)
        <a href="{{ $editRoute }}"
           class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition">
            <x-admin.icon name="edit" class="w-3.5 h-3.5" /> Edit
        </a>
    @endif
    @if($deleteAction ?? false)
        <button type="button"
                @click="confirmDelete = {{ $itemId }}"
                class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition">
            <x-admin.icon name="trash" class="w-3.5 h-3.5" /> Delete
        </button>
    @endif
    @if($viewRoute ?? false)
        <a href="{{ $viewRoute }}"
           target="_blank"
           class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
            <x-admin.icon name="eye" class="w-3.5 h-3.5" /> View
        </a>
    @endif
</div>
