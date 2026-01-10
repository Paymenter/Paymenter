<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\MercadoPago\MercadoPago;

Route::post('/extensions/mercadopago/webhook', [MercadoPago::class, 'webhook'])->name('extensions.gateways.mercadopago.webhook');