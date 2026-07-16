<?php

use App\Livewire\Admin;

// Shared: super_admin + editor
Route::middleware(['auth', 'verified', 'role:super_admin,editor'])->group(function () {
    Route::get('/', Admin\Dashboard::class)->name('dashboard');

    Route::get('/products', Admin\Products\ProductIndex::class)->name('products.index');
    Route::get('/products/create', Admin\Products\ProductForm::class)->name('products.create');
    Route::get('/products/{product}/edit', Admin\Products\ProductForm::class)->name('products.edit');

    Route::get('/categories', Admin\Categories\CategoryIndex::class)->name('categories.index');
    Route::get('/categories/create', Admin\Categories\CategoryForm::class)->name('categories.create');
    Route::get('/categories/{category}/edit', Admin\Categories\CategoryForm::class)->name('categories.edit');

    Route::get('/resellers', Admin\Resellers\ResellerIndex::class)->name('resellers.index');
    Route::get('/resellers/create', Admin\Resellers\ResellerForm::class)->name('resellers.create');
    Route::get('/resellers/{reseller}/edit', Admin\Resellers\ResellerForm::class)->name('resellers.edit');

    Route::get('/articles', Admin\Articles\ArticleIndex::class)->name('articles.index');
    Route::get('/articles/create', Admin\Articles\ArticleForm::class)->name('articles.create');
    Route::get('/articles/{article}/edit', Admin\Articles\ArticleForm::class)->name('articles.edit');

    Route::get('/slides', Admin\Slides\SlideIndex::class)->name('slides.index');
    Route::get('/inquiries', Admin\Inquiries\InquiryIndex::class)->name('inquiries.index');
});

// Restricted: super_admin only
Route::middleware(['auth', 'verified', 'role:super_admin'])->group(function () {
    Route::get('/settings', Admin\Settings\SettingsForm::class)->name('settings');
    Route::get('/users', Admin\Users\UserIndex::class)->name('users.index');
});
