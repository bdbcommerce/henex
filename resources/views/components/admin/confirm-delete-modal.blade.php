<div x-data="{ show: false, itemId: null }"
     @delete-confirm.window="show = true; itemId = $event.detail"
     x-show="show"
     class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
     style="display: none">

    <div class="bg-white rounded-xl shadow-lg max-w-sm w-full" @click.stop>
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-neutral-950">Delete Item?</h3>
        </div>
        <div class="px-6 py-4">
            <p class="text-gray-600">Are you sure you want to delete this item? This action cannot be undone.</p>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
            <button type="button"
                    @click="show = false"
                    class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 transition text-sm font-medium">
                Cancel
            </button>
            <button type="button"
                    @click="show = false; wire.call('{{ $action }}', itemId)"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition text-sm font-medium">
                Delete
            </button>
        </div>
    </div>
</div>
