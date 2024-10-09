<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\Stripe\Stripe;

Route::post('/webhook', [Stripe::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class]);
