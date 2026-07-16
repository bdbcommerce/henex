<?php

use Illuminate\Support\Facades\Route;

// Public routes (cookie-based localization)
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
Route::get('/category/{slug}', [\App\Http\Controllers\ProductController::class, 'category'])->name('products.category');

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

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Admin authentication
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Auth\LogoutController::class, 'destroy'])->name('logout');
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');
});
