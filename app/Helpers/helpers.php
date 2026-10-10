<?php

use App\Support\SiteData;

if (! function_exists('settings')) {
    /**
     * Get a setting value. All settings are loaded with one cached query per request
     * (see App\Support\SiteData::settings); empty values fall back to $default.
     */
    function settings(string $key, mixed $default = null): mixed
    {
        $value = SiteData::settings()[$key] ?? null;

        return $value !== null && $value !== '' ? $value : $default;
    }
}
