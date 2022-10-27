<?php

use Illuminate\Support\Facades\Route;
use App\Extensions\Gateways\Stripe\index;
include(__DIR__ . '/index.php');


Route::post('/stripe/webhook', function () {
    webhook(request());
});