<?php

use Illuminate\Support\Facades\Route;

include_once __DIR__ . '/index.php';

Route::post('/mollie/webhook', function () {
    Mollie_webhook(request());
});
