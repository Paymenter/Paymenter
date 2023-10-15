<?php

use Illuminate\Support\Facades\Route;


Route::post('/mollie/webhook', [App\Extensions\Gateways\Mollie\Mollie::class, 'webhook']);
