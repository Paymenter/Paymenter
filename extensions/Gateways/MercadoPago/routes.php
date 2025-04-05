<?php

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\MercadoPago\MercadoPago;

Route::post('/extensions/gateways/mercadopago/webhook', [MercadoPago::class, 'webhook'])->withoutMiddleware([VerifyCsrfToken::class])->name('extensions.gateways.mercadopago.webhook');
