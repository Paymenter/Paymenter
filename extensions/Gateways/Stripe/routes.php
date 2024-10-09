<?php

use Paymenter\Extensions\Gateways\Stripe\Stripe;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::post('/webhook', [Stripe::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class]);
