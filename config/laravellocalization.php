<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Supported Locales
    |--------------------------------------------------------------------------
    |
    | Locales that the application supports. Each locale should be a valid
    | ISO 639-1 language code. The key is the locale code and the value
    | is the native name of the locale.
    |
    */
    'supportedLocales' => [
        'uz' => [
            'name'     => "O'zbek",
            'script'   => 'Latn',
            'native'   => "O'zbek",
            'regional' => 'uz_UZ',
        ],
        'ru' => [
            'name'     => 'Русский',
            'script'   => 'Cyrl',
            'native'   => 'Русский',
            'regional' => 'ru_RU',
        ],
        'en' => [
            'name'     => 'English',
            'script'   => 'Latn',
            'native'   => 'English',
            'regional' => 'en_US',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Hide Default Locale In URL
    |--------------------------------------------------------------------------
    |
    | If set to true, the default locale will not be shown in the URL.
    | For example, /uz/products becomes just /products
    | We want to always show the locale prefix, so set to false.
    |
    */
    'hideDefaultLocaleInURL' => false,

    /*
    |--------------------------------------------------------------------------
    | Default Locale
    |--------------------------------------------------------------------------
    |
    | The default locale for the application. This should match your APP_LOCALE
    | in the .env file.
    |
    */
    'defaultLocale' => env('APP_LOCALE', 'uz'),

    /*
    |--------------------------------------------------------------------------
    | Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale when a translation key is not found.
    |
    */
    'fallbackLocale' => env('APP_FALLBACK_LOCALE', 'uz'),

    /*
    |--------------------------------------------------------------------------
    | Use Cookies to Store Locale
    |--------------------------------------------------------------------------
    |
    | Set the cookie name to use for storing the locale preference.
    | Set to null to disable cookie-based locale detection.
    |
    */
    'localeSessionVariableName' => 'locale',

    /*
    |--------------------------------------------------------------------------
    | Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | The middleware will detect the locale from the URL or session and
    | set it as the application locale.
    |
    */
    'middleware' => [
        'useSessionLocale' => true,
        'useCookieLocale'  => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Locale Mapping
    |--------------------------------------------------------------------------
    |
    | Map URL locale codes to full locale strings if needed.
    | Leave empty if your locale codes match your full locale strings.
    |
    */
    'localeMapping' => [],
];
