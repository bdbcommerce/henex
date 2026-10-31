<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class LocaleController extends Controller
{
    public function setLocale(Request $request, string $locale)
    {
        $supportedLocales = config('laravellocalization.supportedLocales', ['uz', 'ru', 'en']);

        if (!array_key_exists($locale, $supportedLocales)) {
            abort(400, 'Unsupported locale');
        }

        return redirect()->back()
            ->cookie('locale', $locale, 60 * 24 * 365); // 1 year
    }
}
