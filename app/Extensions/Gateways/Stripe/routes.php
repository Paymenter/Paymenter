<?php

use Illuminate\Support\Facades\Route;

include_once __DIR__ . '/index.php';

Route::post('/stripe/webhook', function () {
    Stripe_webhook(request());
});
