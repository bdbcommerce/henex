<?php

namespace App\Providers;

use App\Support\SiteData;
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
        // Loaded once per request (SiteData) and never overriding data a controller already passed.
        View::composer(['pages.*', 'livewire.frontend.*', 'layouts.app'], function ($view) {
            if (! $view->offsetExists('categories')) {
                $view->with('categories', SiteData::categories());
            }
            if (! $view->offsetExists('regions')) {
                $view->with('regions', SiteData::regions());
            }
        });
    }
}
