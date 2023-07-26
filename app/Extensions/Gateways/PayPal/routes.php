<?php

use Illuminate\Support\Facades\Route;


Route::post('/paypal/webhook', [App\Extensions\Gateways\PayPal\PayPal::class, 'webhook'])->name('paypal.webhook');
