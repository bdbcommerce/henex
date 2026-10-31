<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocaleFromUrl
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = config('laravellocalization.supportedLocales', ['uz', 'ru', 'en']);

        // Check for locale in cookie, default to app.locale
        $locale = $request->cookie('locale', config('app.locale', 'uz'));

        if (array_key_exists($locale, $supportedLocales)) {
            app()->setLocale($locale);
        } else {
            app()->setLocale(config('app.locale', 'uz'));
        }

        return $next($request);
    }
}
