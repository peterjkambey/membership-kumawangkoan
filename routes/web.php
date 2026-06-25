<?php

use App\Http\Controllers\PublicCardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// E-Membership Card - Public
Route::get('/card/{member}', [PublicCardController::class, 'show'])->name('ecard.show');
Route::get('/api/card/{member}', [PublicCardController::class, 'cardData'])->name('ecard.api');
