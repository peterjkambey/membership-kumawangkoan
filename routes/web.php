<?php

use App\Http\Controllers\PublicCardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin');
});

// E-Membership Card - Public
Route::get('/card/{member}', [PublicCardController::class, 'show'])->name('ecard.show');
Route::get('/api/card/{member}', [PublicCardController::class, 'cardData'])->name('ecard.api');

// Xendit Webhook (tanpa CSRF — dari eksternal)
Route::post('/api/xendit/webhook', [App\Http\Controllers\XenditWebhookController::class, 'callback'])
    ->name('xendit.webhook')
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Simulasi callback (hanya untuk admin/test)
Route::post('/api/xendit/simulate', [App\Http\Controllers\XenditWebhookController::class, 'simulateCallback'])
    ->name('xendit.simulate')
    ->middleware(['auth:web']);
