<div x-data="{ deleteId: null }">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-neutral-950">Industries</h2>
            <p class="text-sm text-gray-500">Fields of activity clients choose when registering in the Telegram bot.</p>
        </div>
        <a href="{{ route('admin.industries.create') }}" class="inline-flex items-center gap-1.5 bg-brand text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-brand-dark transition"><x-admin.icon name="plus" class="w-4 h-4" /> Add Industry</a>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left w-20">Order</th><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Recommended categories</th><th class="px-4 py-3 text-left">Bot users</th><th class="px-4 py-3 text-left">Active</th><th class="px-4 py-3 text-left">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $ind)
                <tr class="hover:bg-gray-50" wire:key="ind-{{ $ind->id }}">
                    <td class="px-4 py-3 text-gray-400">
                        <button type="button" wire:click="move({{ $ind->id }}, -1)" @disabled($loop->first) class="px-1 hover:text-gray-700 disabled:opacity-30">↑</button>
                        <button type="button" wire:click="move({{ $ind->id }}, 1)" @disabled($loop->last) class="px-1 hover:text-gray-700 disabled:opacity-30">↓</button>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold">{{ $ind->emoji }} {{ $ind->getTranslation('name', 'uz') }}</div>
                        <div class="text-xs text-gray-400">{{ $ind->getTranslation('name', 'ru') }} · {{ $ind->getTranslation('name', 'en') }}</div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1 max-w-sm">
                            @foreach($ind->categories as $c)
                                <span class="px-2 py-0.5 rounded-md bg-gray-100 text-gray-700 text-xs">{{ $c->getTranslation('name', 'en') }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $ind->telegram_users_count }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $ind->is_active ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-500' }}">{{ $ind->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.industries.edit', $ind) }}" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 transition"><x-admin.icon name="edit" class="w-3.5 h-3.5" /> Edit</a>
                            <button type="button" @click="deleteId = {{ $ind->id }}" class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition"><x-admin.icon name="trash" class="w-3.5 h-3.5" /> Delete</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No industries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div x-show="deleteId" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display: none">
        <div class="bg-white rounded-xl shadow-lg max-w-sm w-full" @click.stop>
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="text-lg font-bold text-neutral-950">Delete Industry?</h3></div>
            <div class="px-6 py-4"><p class="text-gray-600">Bot users who chose it keep their account but lose this field. Consider making it inactive instead.</p></div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                <button type="button" @click="deleteId = null" class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 transition font-medium">Cancel</button>
                <button type="button" @click="$wire.call('delete', deleteId); deleteId = null" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">Delete</button>
            </div>
        </div>
    </div>
</div>
