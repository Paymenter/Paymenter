<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\UddoktaPay\UddoktaPay;

Route::post('/extensions/uddoktapay/webhook', [UddoktaPay::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class])->name('extensions.gateways.uddoktapay.webhook');
