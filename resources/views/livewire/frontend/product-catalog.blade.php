<div x-data="{ view: 'grid' }">
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Sidebar --}}
        <aside class="lg:w-56 flex-shrink-0">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <h3 class="font-bold text-neutral-950 text-sm mb-3 uppercase tracking-wide">{{ __('site.products.categories') }}</h3>
                <div class="space-y-1">
                    <button wire:click="$set('category', '')"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm transition {{ $category === '' ? 'bg-brand text-white font-semibold' : 'hover:bg-gray-50 text-gray-700' }}">
                        {{ __('site.products.all_categories') }}
                    </button>
                    @foreach($categories as $cat)
                    <button wire:click="$set('category', '{{ $cat->slug }}')"
                            class="w-full text-left px-3 py-2 rounded-lg text-sm transition {{ $category === $cat->slug ? 'bg-brand text-white font-semibold' : 'hover:bg-gray-50 text-gray-700' }}">
                        {{ $cat->name }}
                    </button>
                        @foreach($cat->children as $child)
                        <button wire:click="$set('category', '{{ $child->slug }}')"
                                class="w-full text-left pl-6 pr-3 py-1.5 rounded-lg text-xs transition {{ $category === $child->slug ? 'bg-brand/10 text-brand font-semibold' : 'hover:bg-gray-50 text-gray-500' }}">
                            {{ $child->name }}
                        </button>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 min-w-0">
            {{-- Toolbar --}}
            <div class="flex flex-wrap items-center gap-3 mb-6">
                <div class="flex-1 min-w-0">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input wire:model.live.debounce.300ms="search" type="text"
                               placeholder="{{ __('site.products.search_placeholder') }}"
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm focus:ring-brand focus:border-brand shadow-sm">
                    </div>
                </div>
                <select wire:model.live="sort" class="rounded-xl border-gray-300 text-sm shadow-sm focus:ring-brand focus:border-brand py-2.5">
                    <option value="sort_order">{{ __('site.products.sort_default') }}</option>
                    <option value="newest">{{ __('site.products.sort_newest') }}</option>
                    <option value="name_asc">{{ __('site.products.sort_name_asc') }}</option>
                    <option value="name_desc">{{ __('site.products.sort_name_desc') }}</option>
                </select>
                <div class="flex border border-gray-300 rounded-xl overflow-hidden">
                    <button @click="view='grid'" :class="view==='grid' ? 'bg-brand text-white' : 'bg-white text-gray-400'" class="px-3 py-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                    </button>
                    <button @click="view='list'" :class="view==='list' ? 'bg-brand text-white' : 'bg-white text-gray-400'" class="px-3 py-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>

            {{-- Results count --}}
            <p class="text-sm text-gray-500 mb-5">
                {{ __('site.products.showing', ['count' => $products->total()]) }}
            </p>

            {{-- Grid view --}}
            <div x-show="view==='grid'" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse($products as $product)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg transition-shadow group flex flex-col overflow-hidden">
                    <a href="{{ route('products.show', $product->slug) }}" class="block aspect-square bg-gray-50 overflow-hidden">
                        @if($product->getFirstMediaUrl('gallery'))
                        <img src="{{ $product->getFirstMediaUrl('gallery', 'thumb') ?: $product->getFirstMediaUrl('gallery') }}"
                             alt="{{ $product->name }}"
                             class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform duration-500">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-gray-200">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        @endif
                    </a>
                    <div class="p-4 flex flex-col flex-1">
                        @if($product->sku)
                        <p class="text-xs text-gray-400 font-mono mb-1">{{ $product->sku }}</p>
                        @endif
                        <h3 class="font-bold text-neutral-950 text-sm leading-snug mb-3 flex-1">
                            <a href="{{ route('products.show', $product->slug) }}" class="hover:text-brand transition">
                                {{ $product->name }}
                            </a>
                        </h3>
                        <div class="flex gap-2">
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="flex-1 text-center bg-brand hover:bg-brand-dark text-white text-xs font-semibold py-2 px-3 rounded-lg transition">
                                {{ __('site.cta.details') }}
                            </a>
                            <button @click="$dispatch('open-quote-modal', { productId: {{ $product->id }}, productName: '{{ addslashes($product->name) }}' })"
                                    class="flex-1 text-center border border-brand text-brand hover:bg-brand hover:text-white text-xs font-semibold py-2 px-3 rounded-lg transition">
                                {{ __('site.cta.request_quote') }}
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-20 text-gray-400">
                    <div class="text-5xl mb-4">🔍</div>
                    <p class="text-lg">{{ __('site.products.no_results') }}</p>
                </div>
                @endforelse
            </div>

            {{-- List view --}}
            <div x-show="view==='list'" class="space-y-3" x-cloak>
                @foreach($products as $product)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 p-4 group">
                    <div class="w-20 h-20 flex-shrink-0 bg-gray-50 rounded-lg overflow-hidden">
                        @if($product->getFirstMediaUrl('gallery'))
                        <img src="{{ $product->getFirstMediaUrl('gallery') }}" alt="{{ $product->name }}" class="w-full h-full object-contain p-1">
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-gray-400 font-mono">{{ $product->sku }}</p>
                        <h3 class="font-bold text-neutral-950 text-sm group-hover:text-brand transition">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1 line-clamp-1">{{ $product->short_description }}</p>
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <a href="{{ route('products.show', $product->slug) }}" class="bg-brand text-white text-xs font-semibold py-2 px-4 rounded-lg hover:bg-brand-dark transition">{{ __('site.cta.details') }}</a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($products->hasPages())
            <div class="mt-10 flex justify-center">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
</div>
