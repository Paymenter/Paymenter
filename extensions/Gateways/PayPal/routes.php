<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\PayPal\PayPal;

Route::get('/extensions/paypal/webhook', [PayPal::class, 'webhook'])->name('extensions.gateways.paypal.webhook');
Route::get('/extensions/paypal/capture', [PayPal::class, 'capture'])->name('extensions.gateways.paypal.capture');
