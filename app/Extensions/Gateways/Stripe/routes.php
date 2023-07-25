<?php

use Illuminate\Support\Facades\Route;

Route::post('/stripe/webhook', [App\Extensions\Gateways\Stripe\Stripe::class, 'webhook']);
