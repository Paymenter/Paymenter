<?php

use Illuminate\Support\Facades\Route;

include_once __DIR__ . '/index.php';

Route::post('/paypal/webhook', function () {
    PayPal_webhook(request());
});
