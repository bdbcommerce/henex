@props(['items' => [], 'dark' => false])

<nav aria-label="breadcrumb" class="{{ $dark ? 'text-gray-300' : 'text-gray-500' }} text-sm">
    <ol class="flex items-center flex-wrap gap-1">
        @foreach($items as $index => [$label, $url])
            <li class="flex items-center gap-1">
                @if($url)
                    <a href="{{ $url }}" class="{{ $dark ? 'hover:text-white' : 'hover:text-brand' }} transition">
                        {{ $label }}
                    </a>
                @else
                    <span class="{{ $dark ? 'text-white' : 'text-neutral-950' }} font-medium">{{ $label }}</span>
                @endif

                @if(!$loop->last)
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
