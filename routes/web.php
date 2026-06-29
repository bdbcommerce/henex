<?php

use Illuminate\Support\Facades\Route;

// Public routes (cookie-based localization)
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');

// Where to Buy / Resellers
Route::get('/where-to-buy', [\App\Http\Controllers\ResellerController::class, 'index'])->name('resellers');

// News / Articles
Route::get('/news', [\App\Http\Controllers\ArticleController::class, 'index'])->name('news.index');
Route::get('/news/{article:slug}', [\App\Http\Controllers\ArticleController::class, 'show'])->name('news.show');

// Contact
Route::get('/contact', [\App\Http\Controllers\ContactController::class, 'index'])->name('contact');
Route::post('/contact', [\App\Http\Controllers\ContactController::class, 'store'])->name('contact.store');

// About
Route::get('/about', [\App\Http\Controllers\StaticPageController::class, 'about'])->name('about');

// Locale Switcher
Route::post('/set-locale/{locale}', [\App\Http\Controllers\LocaleController::class, 'setLocale'])
    ->where('locale', 'uz|ru|en')
    ->name('locale.set');

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('/admin')->name('admin.')->group(function () {
    Route::get('/', \App\Livewire\Admin\Dashboard::class)->name('dashboard');

    // Products
    Route::get('/products', \App\Livewire\Admin\Products\ProductIndex::class)->name('products.index');
    Route::get('/products/create', \App\Livewire\Admin\Products\ProductForm::class)->name('products.create');
    Route::get('/products/{product}/edit', \App\Livewire\Admin\Products\ProductForm::class)->name('products.edit');

    // Categories
    Route::get('/categories', \App\Livewire\Admin\Categories\CategoryIndex::class)->name('categories.index');
    Route::get('/categories/create', \App\Livewire\Admin\Categories\CategoryForm::class)->name('categories.create');
    Route::get('/categories/{category}/edit', \App\Livewire\Admin\Categories\CategoryForm::class)->name('categories.edit');

    // Resellers
    Route::get('/resellers', \App\Livewire\Admin\Resellers\ResellerIndex::class)->name('resellers.index');
    Route::get('/resellers/create', \App\Livewire\Admin\Resellers\ResellerForm::class)->name('resellers.create');
    Route::get('/resellers/{reseller}/edit', \App\Livewire\Admin\Resellers\ResellerForm::class)->name('resellers.edit');

    // Articles
    Route::get('/articles', \App\Livewire\Admin\Articles\ArticleIndex::class)->name('articles.index');
    Route::get('/articles/create', \App\Livewire\Admin\Articles\ArticleForm::class)->name('articles.create');
    Route::get('/articles/{article}/edit', \App\Livewire\Admin\Articles\ArticleForm::class)->name('articles.edit');

    // Slides
    Route::get('/slides', \App\Livewire\Admin\Slides\SlideIndex::class)->name('slides.index');

    // Inquiries
    Route::get('/inquiries', \App\Livewire\Admin\Inquiries\InquiryIndex::class)->name('inquiries.index');

    // Settings (super_admin only)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/settings', \App\Livewire\Admin\Settings\SettingsForm::class)->name('settings');
        Route::get('/users', \App\Livewire\Admin\Users\UserIndex::class)->name('users.index');
    });
});

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Authentication routes (Laravel Breeze)
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'store'])->name('login.store');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Auth\LogoutController::class, 'destroy'])->name('logout');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
});
