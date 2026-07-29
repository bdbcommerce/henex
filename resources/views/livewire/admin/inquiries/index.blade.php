<div>
    <h2 class="text-xl font-bold text-neutral-950 mb-6">Inquiries</h2>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm divide-y divide-gray-100">
        @forelse($inquiries as $inquiry)
        <div class="px-5 py-4 {{ $inquiry->is_read ? '' : 'bg-brand/5' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-1">
                        @if(!$inquiry->is_read)<span class="w-2 h-2 rounded-full bg-brand flex-shrink-0 mt-1"></span>@endif
                        <span class="font-semibold text-sm">{{ $inquiry->name }}</span>
                        @if($inquiry->company)<span class="text-gray-400 text-xs">· {{ $inquiry->company }}</span>@endif
                        <span class="text-gray-400 text-xs">· {{ $inquiry->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500 mb-2">
                        <span class="inline-flex items-center gap-1"><x-admin.icon name="phone" class="w-3.5 h-3.5 text-gray-400" /> {{ $inquiry->phone }}</span>
                        @if($inquiry->email)<span class="inline-flex items-center gap-1"><x-admin.icon name="envelope" class="w-3.5 h-3.5 text-gray-400" /> {{ $inquiry->email }}</span>@endif
                        @if($inquiry->product)<span class="inline-flex items-center gap-1"><x-admin.icon name="box" class="w-3.5 h-3.5 text-gray-400" /> {{ $inquiry->product->getTranslation('name',app()->getLocale()) }}</span>@endif
                    </div>
                    @if($inquiry->message)<p class="text-sm text-gray-600">{{ $inquiry->message }}</p>@endif
                </div>
                @if(!$inquiry->is_read)
                <button wire:click="markRead({{ $inquiry->id }})" class="text-xs text-gray-400 hover:text-brand border border-gray-200 px-3 py-1 rounded-lg flex-shrink-0">Mark read</button>
                @endif
            </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center text-gray-400 text-sm">No inquiries yet.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $inquiries->links() }}</div>
</div>
