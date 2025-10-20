<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\Stripe\Stripe;

Route::post('/extensions/stripe/webhook', [Stripe::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class])->name('extensions.gateways.stripe.webhook');
Route::get('/extensions/stripe/setup-agreement', [Stripe::class, 'setupAgreement'])
    ->middleware(['web', 'auth'])
    ->name('extensions.gateways.stripe.setup-agreement');
