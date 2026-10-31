<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use App\Models\Region;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Collection;

/**
 * Shared look-up data for public pages, loaded at most once per request.
 *
 * Eloquent collections are NOT stored in the cache: Laravel 13 refuses to
 * unserialize objects from cache (config/cache.php → serializable_classes),
 * which turned cached models into __PHP_Incomplete_Class and crashed pages.
 */
class SiteData
{
    /** Active root categories with their children (nav, catalog sidebar, footer). */
    public static function categories(): Collection
    {
        return once(fn () => Category::where('is_active', true)->whereNull('parent_id')
            ->with('children')->orderBy('sort_order')->get());
    }

    public static function regions(): Collection
    {
        return once(fn () => Region::orderBy('sort_order')->get());
    }

    /** Active products for "which product?" selects (id + name only). */
    public static function productOptions(): Collection
    {
        return once(fn () => Product::where('is_active', true)->orderBy('sort_order')->get(['id', 'name']));
    }

    /**
     * All settings as key => value. One query per hour at most (plain array → safe to cache),
     * and never re-queried within a request. Cleared by Setting::saved/deleted.
     */
    public static function settings(): array
    {
        return once(fn () => cache()->remember(self::SETTINGS_KEY, 3600, fn () => Setting::pluck('value', 'key')->all()));
    }

    public const SETTINGS_KEY = 'settings.all';
}
