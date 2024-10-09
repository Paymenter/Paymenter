<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\PayPal\PayPal;

Route::post('/capture', [PayPal::class, 'capture'])->withoutMiddleware([VerifyCsrfToken::class])->name('capture');
Route::post('/webhook', [PayPal::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class])->name('webhook');
