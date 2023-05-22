<?php

use Illuminate\Support\Facades\Route;

include_once __DIR__ . '/index.php';

Route::post('/paypal_ipn/webhook', function () {
    PayPal_IPN_webhook(request());
})->name('paypal_ipn.webhook');
