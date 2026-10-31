<div>
    <h2 class="text-xl font-bold text-neutral-950 mb-6">Users</h2>
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        <table class="w-full text-sm"><thead class="bg-gray-50 text-gray-500 uppercase text-xs"><tr><th class="px-4 py-3 text-left">Name</th><th class="px-4 py-3 text-left">Email</th><th class="px-4 py-3 text-left">Roles</th><th class="px-4 py-3 text-left">Joined</th></tr></thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($users as $user)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-semibold">{{ $user->name }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $user->email }}</td>
                <td class="px-4 py-3">@foreach($user->roles as $role)<span class="bg-brand/10 text-brand text-xs px-2 py-0.5 rounded-full mr-1">{{ $role->name }}</span>@endforeach</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $user->created_at->format('d.m.Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No users.</td></tr>
            @endforelse
        </tbody></table>
        <div class="px-4 py-3 border-t border-gray-100">{{ $users->links() }}</div>
    </div>
</div>
