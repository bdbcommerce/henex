<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Region;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share root categories + regions with every public view
        View::composer(['pages.*', 'livewire.frontend.*', 'layouts.app'], function ($view) {
            $view->with('categories',
                Category::where('is_active', true)
                    ->whereNull('parent_id')
                    ->orderBy('sort_order')
                    ->get()
            );
            $view->with('regions',
                Region::orderBy('sort_order')->limit(5)->get()
            );
        });
    }
}
