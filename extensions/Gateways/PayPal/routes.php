<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\PayPal\PayPal;

Route::prefix('/extensions/paypal', function () {
    Route::get('/webhook', [PayPal::class, 'webhook'])->name('extensions.gateways.paypal.webhook');
    Route::get('/capture', [PayPal::class, 'capture'])->name('extensions.gateways.paypal.capture');
});