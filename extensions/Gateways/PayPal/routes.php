<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\PayPal\PayPal;

Route::post('/extensions/paypal/webhook', [PayPal::class, 'webhook'])->name('extensions.gateways.paypal.webhook');
Route::post('/extensions/paypal/capture', [PayPal::class, 'capture'])->name('extensions.gateways.paypal.capture');
