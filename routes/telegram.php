<?php

use App\Http\Controllers\Telegram\WebAppController;
use App\Http\Controllers\Telegram\WebhookController;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;

// Bot webhook — no session/CSRF; authenticated by the X-Telegram-Bot-Api-Secret-Token header.
Route::post('/telegram/webhook', WebhookController::class)
    ->middleware('throttle:300,1')
    ->name('telegram.webhook');

// Mini App (opened inside Telegram). Public read-only pages.
Route::prefix('tg')->name('tg.')->middleware(SubstituteBindings::class)->group(function () {
    Route::get('/', [WebAppController::class, 'home'])->name('home');
    Route::get('/category/{slug}', [WebAppController::class, 'category'])->name('category');
    Route::get('/product/{product:slug}', [WebAppController::class, 'product'])->name('product');
    Route::get('/resellers', [WebAppController::class, 'resellers'])->name('resellers');
    // Authenticated by Telegram-signed initData in the body, so no CSRF token needed.
    Route::post('/inquiry', [WebAppController::class, 'inquiry'])->middleware('throttle:20,1')->name('inquiry');
});
