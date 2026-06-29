<div x-data="{ deleteId: null }">
    {{-- Header with title and create button --}}
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h1 class="text-3xl font-bold text-neutral-950">Products</h1>
                <p class="text-gray-600 text-sm mt-1">Manage all product listings and inventory</p>
            </div>
            <a href="{{ route('admin.products.create') }}" class="bg-brand hover:bg-brand-dark text-white px-6 py-2 rounded-lg font-medium transition inline-flex items-center gap-2">
                <span>+</span> Add New Product
            </a>
        </div>
    </div>

    {{-- Filters and Search --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <input type="text"
                       placeholder="Search by name or SKU..."
                       wire:model.live.debounce.300ms="search"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand text-sm">
            </div>
            <div>
                <select wire:model.live="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand text-sm">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div>
                <select wire:model.live="sortBy" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:border-brand focus:ring-1 focus:ring-brand text-sm">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="featured">Featured Only</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">SKU</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Product Name</th>
                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Categories</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Status</th>
                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Featured</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4">
                            <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-700">{{ $product->sku ?? '—' }}</code>
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="font-medium text-neutral-950">{{ $product->getTranslation('name', app()->getLocale()) }}</p>
                                <p class="text-xs text-gray-600 mt-1">{{ str_replace('-', ' ', $product->slug) }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-1 flex-wrap">
                                @forelse($product->categories as $category)
                                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                        {{ $category->getTranslation('name', app()->getLocale()) }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-500">No categories</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleActive({{ $product->id }})" class="text-lg hover:scale-110 transition">
                                {{ $product->is_active ? '✅' : '⭕' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-center">
                            {{ $product->is_featured ? '⭐' : '·' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex gap-2 justify-end">
                                <a href="{{ route('admin.products.edit', $product) }}"
                                   class="px-3 py-1 text-xs rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition font-medium">
                                    Edit
                                </a>
                                <button type="button"
                                        @click="deleteId = {{ $product->id }}"
                                        class="px-3 py-1 text-xs rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition font-medium">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <p class="text-gray-500 text-sm">No products found. Create your first product to get started.</p>
                            <a href="{{ route('admin.products.create') }}" class="text-brand text-sm mt-2 hover:underline inline-block">+ Create Product</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <p class="text-sm text-gray-600">Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() }}</p>
            <div class="flex gap-2">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    <div x-show="deleteId" x-transition class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" style="display: none">
        <div class="bg-white rounded-xl shadow-lg max-w-sm w-full" @click.stop>
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-neutral-950">Delete Product?</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-gray-600">This will permanently delete the product and cannot be undone.</p>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end">
                <button type="button"
                        @click="deleteId = null"
                        class="px-4 py-2 text-gray-600 rounded-lg hover:bg-gray-100 transition font-medium">
                    Cancel
                </button>
                <button type="button"
                        @click="wire.call('delete', deleteId); deleteId = null"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>

