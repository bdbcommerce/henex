<div>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-neutral-950">Dashboard</h1>
        <p class="text-gray-600 mt-1">Welcome back! Here's what's happening with your site.</p>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($stats as $stat)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">{{ $stat['label'] }}</p>
                        <p class="text-3xl font-bold text-neutral-950 mt-1">{{ $stat['value'] }}</p>
                    </div>
                    <div class="text-4xl">{{ $stat['icon'] }}</div>
                </div>
                <p class="text-xs text-gray-500">{{ $stat['change'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Recent Activity Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent Inquiries --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-neutral-950">Recent Inquiries</h2>
                <a href="{{ route('admin.inquiries.index') }}" class="text-xs text-brand hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                @forelse($recentInquiries as $inquiry)
                    <div class="px-6 py-4 hover:bg-gray-50 transition {{ !$inquiry->is_read ? 'bg-brand/5' : '' }}">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="font-medium text-neutral-950 text-sm">{{ $inquiry->name }}</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $inquiry->product?->getTranslation('name', app()->getLocale()) ?? 'General inquiry' }}
                                </p>
                            </div>
                            @if(!$inquiry->is_read)
                                <span class="inline-block w-2 h-2 bg-brand rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-2">{{ $inquiry->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center text-gray-500 text-sm">
                        No inquiries yet
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Products & Articles --}}
        <div class="space-y-6">
            {{-- Products --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-neutral-950">Recent Products</h2>
                    <a href="{{ route('admin.products.index') }}" class="text-xs text-brand hover:underline">View all →</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentProducts as $product)
                        <div class="px-6 py-3 hover:bg-gray-50 transition text-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-neutral-950">{{ $product->getTranslation('name', app()->getLocale()) }}</p>
                                    <p class="text-xs text-gray-600">{{ $product->sku ?? 'No SKU' }}</p>
                                </div>
                                <span class="inline-block px-2 py-1 text-xs rounded-full {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 text-sm">
                            No products yet
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Articles --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-neutral-950">Recent Articles</h2>
                    <a href="{{ route('admin.articles.index') }}" class="text-xs text-brand hover:underline">View all →</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentArticles as $article)
                        <div class="px-6 py-3 hover:bg-gray-50 transition text-sm">
                            <div class="flex items-center justify-between">
                                <p class="font-medium text-neutral-950 line-clamp-1">{{ $article->getTranslation('title', app()->getLocale()) }}</p>
                                <span class="inline-block px-2 py-1 text-xs rounded-full {{ $article->is_published ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $article->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-center text-gray-500 text-sm">
                            No articles yet
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
