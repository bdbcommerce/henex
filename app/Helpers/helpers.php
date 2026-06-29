<?php

use App\Models\Setting;

if (! function_exists('settings')) {
    /**
     * Get a setting value from the database (with 1-hour cache).
     */
    function settings(string $key, mixed $default = null): mixed
    {
        return cache()->remember("setting_{$key}", 3600, function () use ($key, $default) {
            $value = Setting::find($key)?->value;
            return $value !== null ? $value : $default;
        });
    }
}
