@php($lang = app()->getLocale())
<!DOCTYPE html>
<html lang="{{ $lang }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex">
    <title>@yield('title', 'HENEX')</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    @vite(['resources/css/app.css'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
    <style>
        /* Telegram theme colours with HENEX fallbacks (outside Telegram) */
        :root {
            --bg: var(--tg-theme-bg-color, #ffffff);
            --bg2: var(--tg-theme-secondary-bg-color, #f4f4f5);
            --text: var(--tg-theme-text-color, #0a0a0a);
            --hint: var(--tg-theme-hint-color, #71717a);
            --link: var(--tg-theme-link-color, #D35400);
            --btn: var(--tg-theme-button-color, #FF8C42);
            --btn-text: var(--tg-theme-button-text-color, #ffffff);
        }
        body { background: var(--bg); color: var(--text); -webkit-tap-highlight-color: transparent; }
        .card { background: var(--bg2); }
        .hint { color: var(--hint); }
        .link { color: var(--link); }
        .btn { background: var(--btn); color: var(--btn-text); }
        .chip-on { background: var(--btn); color: var(--btn-text); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .prose-tg { font-size: .875rem; line-height: 1.5; }
        .prose-tg ul { list-style: disc; padding-left: 1.25rem; }
        .prose-tg p + p { margin-top: .5rem; }
    </style>
</head>
<body class="font-sans antialiased min-h-screen pb-20">
    <main class="max-w-xl mx-auto px-4 pt-4">
        @yield('content')
    </main>

    {{-- Bottom tabs --}}
    <nav class="fixed bottom-0 inset-x-0 border-t border-black/5 card">
        <div class="max-w-xl mx-auto grid grid-cols-2 text-center text-xs font-medium">
            <a href="{{ route('tg.home', ['lang' => $lang]) }}" class="py-3 {{ request()->routeIs('tg.home', 'tg.category', 'tg.product') ? 'link' : 'hint' }}">🛍 {{ __('bot.webapp.catalog') }}</a>
            <a href="{{ route('tg.resellers', ['lang' => $lang]) }}" class="py-3 {{ request()->routeIs('tg.resellers') ? 'link' : 'hint' }}">📍 {{ __('bot.webapp.resellers') }}</a>
        </div>
    </nav>

    <script>
        const tg = window.Telegram?.WebApp;
        if (tg) {
            tg.ready();
            tg.expand();
            @if(! request()->routeIs('tg.home'))
                tg.BackButton.show();
                tg.BackButton.onClick(() => history.length > 1 ? history.back() : location.href = @js(route('tg.home', ['lang' => $lang])));
            @endif
        }
    </script>
    @stack('scripts')
</body>
</html>
