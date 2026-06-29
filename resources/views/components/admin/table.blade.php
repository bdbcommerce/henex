<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                @if($bulkActions ?? false)
                    <th class="px-6 py-3 text-left w-12">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300">
                    </th>
                @endif
                {{ $headers }}
                <th class="px-6 py-3 text-right text-sm font-semibold text-gray-600 w-20">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            {{ $slot }}
        </tbody>
    </table>
    @if($pagination ?? true)
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50">
            <p class="text-sm text-gray-600">Showing {{ $items->count() }} of {{ $items->total() ?? $items->count() }}</p>
            {{ $items->links() }}
        </div>
    @endif
</div>
