<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\Mollie\Mollie;

Route::post('/extensions/gateways/mollie/webhook', [Mollie::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class])->name('extensions.gateways.mollie.webhook');
