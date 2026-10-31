<div wire:init="loadStatus">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-neutral-950">Telegram Bot</h2>
            <p class="text-sm text-gray-500">Bot connection and registered clients.</p>
        </div>
    </div>

    {{-- Status --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 lg:col-span-2">
            <div class="flex items-start justify-between gap-4">
                <div class="text-sm space-y-1.5 min-w-0">
                    @if($botStatus === null)
                        <p class="text-gray-400">Checking bot status…</p>
                    @elseif(! $botStatus['configured'])
                        <p class="text-red-600 font-medium">TELEGRAM_BOT_TOKEN is not set in .env</p>
                    @else
                        <p><span class="text-gray-500">Bot:</span>
                            @if($botStatus['bot'])
                                <a href="https://t.me/{{ $botStatus['bot']['username'] }}" target="_blank" class="font-semibold text-blue-700 hover:underline">{{ '@' . $botStatus['bot']['username'] }}</a>
                            @else
                                <span class="text-red-600">token rejected by Telegram</span>
                            @endif
                        </p>
                        @php($wh = $botStatus['webhook'] ?? [])
                        <p class="truncate"><span class="text-gray-500">Webhook:</span>
                            @if(! empty($wh['url']))
                                <span class="{{ $wh['url'] === $botStatus['expected_url'] ? 'text-green-700' : 'text-amber-600' }}">{{ $wh['url'] }}</span>
                            @else
                                <span class="text-amber-600">not connected</span>
                            @endif
                        </p>
                        @if(! empty($wh['pending_update_count']))<p><span class="text-gray-500">Pending updates:</span> {{ $wh['pending_update_count'] }}</p>@endif
                        @if(! empty($wh['last_error_message']))
                            <p class="text-red-600"><span class="text-gray-500">Last error:</span> {{ $wh['last_error_message'] }}
                                ({{ \Carbon\Carbon::createFromTimestamp($wh['last_error_date'])->diffForHumans() }})</p>
                        @endif
                    @endif
                    <p><span class="text-gray-500">AI answers:</span>
                        @if($aiEnabled)<span class="text-green-700">on ({{ config('services.ai.model') }})</span>
                        @else<span class="text-amber-600">off — set AI_API_KEY in .env (keyword search only)</span>@endif
                    </p>
                </div>
                @role('super_admin')
                <button type="button" wire:click="connect" wire:loading.attr="disabled"
                        class="shrink-0 inline-flex items-center gap-1.5 bg-brand text-white font-semibold px-4 py-2 rounded-lg text-sm hover:bg-brand-dark transition disabled:opacity-60">
                    <span wire:loading.remove wire:target="connect">Connect webhook</span>
                    <span wire:loading wire:target="connect">Connecting…</span>
                </button>
                @endrole
            </div>
            @if($setupResult)
                <ul class="mt-4 text-xs space-y-0.5 border-t border-gray-100 pt-3">
                    @foreach($setupResult as $step => $ok)
                        <li class="{{ $ok === true ? 'text-green-700' : 'text-red-600' }}">{{ $ok === true ? '✔' : '✘' }} {{ $step }}{{ is_string($ok) ? ': ' . $ok : '' }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="grid grid-cols-2 gap-3">
            @foreach(['registered' => 'Registered', 'active_7d' => 'Active 7 days', 'unfinished' => 'Not finished', 'blocked' => 'Blocked bot'] as $k => $label)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                    <div class="text-2xl font-bold text-neutral-950">{{ $stats[$k] }}</div>
                    <div class="text-xs text-gray-500">{{ $label }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Users --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <div class="p-4 border-b border-gray-100 flex flex-wrap gap-3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search name, phone, @username…" class="w-72 rounded-lg border-gray-300 text-sm shadow-sm">
            <select wire:model.live="industry" class="rounded-lg border-gray-300 text-sm">
                <option value="">All industries</option>
                @foreach($industries as $ind)<option value="{{ $ind->id }}">{{ $ind->emoji }} {{ $ind->getTranslation('name', 'en') }}</option>@endforeach
            </select>
            <select wire:model.live="status" class="rounded-lg border-gray-300 text-sm">
                <option value="registered">Registered</option>
                <option value="unfinished">Registration not finished</option>
                <option value="blocked">Blocked the bot</option>
                <option value="all">All</option>
            </select>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left">Client</th><th class="px-4 py-3 text-left">Phone</th><th class="px-4 py-3 text-left">Industry</th><th class="px-4 py-3 text-left">Lang</th><th class="px-4 py-3 text-left">Inquiries</th><th class="px-4 py-3 text-left">Registered</th><th class="px-4 py-3 text-left">Last seen</th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $u)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-semibold">{{ $u->full_name ?: trim($u->tg_first_name . ' ' . $u->tg_last_name) ?: '—' }}</div>
                        @if($u->username)<a href="https://t.me/{{ $u->username }}" target="_blank" class="text-xs text-blue-700 hover:underline">{{ '@' . $u->username }}</a>@endif
                        @if($u->is_blocked)<span class="ml-1 text-[10px] px-1.5 py-0.5 rounded bg-red-50 text-red-600">blocked</span>@endif
                    </td>
                    <td class="px-4 py-3 font-mono text-xs">@if($u->phone)<a href="tel:{{ $u->phone }}" class="hover:text-brand">{{ $u->phone }}</a>@else — @endif</td>
                    <td class="px-4 py-3">{{ $u->industry ? $u->industry->emoji . ' ' . $u->industry->getTranslation('name', 'en') : '—' }}</td>
                    <td class="px-4 py-3 uppercase text-xs text-gray-500">{{ $u->locale ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($u->inquiries_count)
                            <a href="{{ route('admin.inquiries.index', ['search' => $u->phone]) }}" class="text-blue-700 hover:underline">{{ $u->inquiries_count }}</a>
                        @else <span class="text-gray-400">0</span> @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $u->registered_at?->format('d.m.Y') ?? ($u->state ? 'step: ' . $u->state : '—') }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $u->last_seen_at?->diffForHumans() ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No bot users yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>
    </div>
</div>
