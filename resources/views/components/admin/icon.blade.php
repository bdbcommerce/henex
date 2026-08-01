@props(['name', 'class' => 'w-5 h-5'])

<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="{{ $class }}">
    @switch($name)
        @case('dashboard')
            <rect x="3" y="12" width="4.5" height="8" rx="1" fill="currentColor" stroke="none" />
            <rect x="9.75" y="7" width="4.5" height="13" rx="1" fill="currentColor" stroke="none" />
            <rect x="16.5" y="3" width="4.5" height="17" rx="1" fill="currentColor" stroke="none" />
            @break

        @case('products')
            <path d="M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3z" />
            <path d="M12 12v9M12 12l8-4.5M12 12L4 7.5" />
            @break

        @case('categories')
            <path d="M3.5 7a1.5 1.5 0 0 1 1.5-1.5h4l2 2h8a1.5 1.5 0 0 1 1.5 1.5v8a1.5 1.5 0 0 1-1.5 1.5H5a1.5 1.5 0 0 1-1.5-1.5V7z" />
            @break

        @case('resellers')
            <path d="M4 9.5l1-5.5h14l1 5.5" />
            <path d="M4 9.5a2 2 0 1 0 4 0M8 9.5a2 2 0 1 0 4 0M12 9.5a2 2 0 1 0 4 0M16 9.5a2 2 0 1 0 4 0" />
            <path d="M5 9.5V19h14V9.5" />
            <path d="M9.5 19v-5h5v5" />
            @break

        @case('articles')
            <rect x="4" y="4" width="16" height="16" rx="1.5" />
            <path d="M7.5 8.5h9M7.5 12h9M7.5 15.5h5.5" />
            @break

        @case('slides')
            <rect x="3" y="5" width="18" height="14" rx="1.5" />
            <circle cx="8.5" cy="10" r="1.5" fill="currentColor" stroke="none" />
            <path d="M3.5 16.5l5-4.2 3.5 2.8 3-2.3 6 4.7" />
            @break

        @case('inquiries')
            <path d="M4 5.5h16v10.5H9l-4.5 4V5.5z" />
            @break

        @case('settings')
            <circle cx="12" cy="12" r="3" />
            <path d="M12 2.5v3M12 18.5v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2.5 12h3M18.5 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1" />
            @break

        @case('users')
            <circle cx="9" cy="8" r="3" />
            <path d="M3 20c0-3.3 2.7-6 6-6s6 2.7 6 6" />
            <circle cx="17.5" cy="9" r="2.25" />
            <path d="M15.2 14.3c2.7.5 4.8 2.8 4.8 5.7" />
            @break

        @case('edit')
            <path d="M4 20l4-1 11-11-3-3L5 16l-1 4z" />
            <path d="M14 6l3 3" />
            @break

        @case('trash')
            <path d="M5 7h14" />
            <path d="M9 7V4.5h6V7" />
            <path d="M6.5 7l.8 12.5a1 1 0 0 0 1 .95h7.4a1 1 0 0 0 1-.95L17.5 7" />
            <path d="M10 11v6M14 11v6" />
            @break

        @case('eye')
            <path d="M2.5 12S6.5 5.5 12 5.5 21.5 12 21.5 12 17.5 18.5 12 18.5 2.5 12 2.5 12z" />
            <circle cx="12" cy="12" r="3" />
            @break

        @case('phone')
            <path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1-9.4 0-17-7.6-17-17 0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z" fill="currentColor" stroke="none" />
            @break

        @case('envelope')
            <rect x="3" y="5.5" width="18" height="13" rx="1.5" />
            <path d="M3.5 6.5l8.5 6.5 8.5-6.5" />
            @break

        @case('box')
            <path d="M12 4l7 3.8v8.4L12 20l-7-3.8V7.8L12 4z" />
            <path d="M12 12l7-3.8M12 12v8M12 12L5 8.2" />
            @break

        @case('check-circle')
            <circle cx="12" cy="12" r="8.5" />
            <path d="M8.25 12.5l2.5 2.5 5-5.5" />
            @break

        @case('x-circle')
            <circle cx="12" cy="12" r="8.5" />
            <path d="M9.5 9.5l5 5M14.5 9.5l-5 5" />
            @break

        @case('pause-circle')
            <circle cx="12" cy="12" r="8.5" />
            <path d="M10 9.5v5M14 9.5v5" />
            @break

        @case('star')
            <path d="M12 3.5l2.6 5.3 5.9.85-4.25 4.15 1 5.85L12 16.75l-5.25 2.9 1-5.85L3.5 9.65l5.9-.85L12 3.5z" />
            @break

        @case('plus')
            <path d="M12 5v14M5 12h14" />
            @break

        @case('chevron-up')
            <path d="M6 15l6-6 6 6" />
            @break

        @case('chevron-down')
            <path d="M6 9l6 6 6-6" />
            @break

        @case('logout')
            <path d="M9 19H5.5A1.5 1.5 0 0 1 4 17.5v-11A1.5 1.5 0 0 1 5.5 5H9" />
            <path d="M16 16l4-4-4-4" />
            <path d="M20 12H9" />
            @break

        @case('external')
            <path d="M14 4.5h5.5V10" />
            <path d="M19.5 4.5L10.5 13.5" />
            <path d="M18 13.5V19a1 1 0 0 1-1 1H5.5a1 1 0 0 1-1-1V7.5a1 1 0 0 1 1-1H11" />
            @break

        @case('folder')
            <path d="M3.5 7a1.5 1.5 0 0 1 1.5-1.5h4l2 2h8a1.5 1.5 0 0 1 1.5 1.5v8a1.5 1.5 0 0 1-1.5 1.5H5a1.5 1.5 0 0 1-1.5-1.5V7z" />
            @break

        @case('close')
            <path d="M6 6l12 12M18 6L6 18" />
            @break

        @case('arrow-left')
            <path d="M19 12H5M11 6l-6 6 6 6" />
            @break
    @endswitch
</svg>
