<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\PayTheFly\PayTheFly;

Route::post('/extensions/paythefly/webhook', [PayTheFly::class, 'webhook'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->name('extensions.gateways.paythefly.webhook');
