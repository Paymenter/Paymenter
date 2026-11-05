<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\PayPal\PayPal;

Route::post('/extensions/paypal/webhook', [PayPal::class, 'webhook'])->name('extensions.gateways.paypal.webhook');
Route::post('/extensions/paypal/capture/{invoice}/{orderID}', [PayPal::class, 'capture'])
    ->where(['orderID' => '[0-9A-Za-z\-]+'])
    ->middleware(['web', 'auth', 'can:view,invoice'])
    ->name('extensions.gateways.paypal.capture');
Route::get('/extensions/paypal/setup-agreement', [PayPal::class, 'setupAgreement'])
    ->middleware(['web', 'auth'])
    ->name('extensions.gateways.paypal.setup-agreement');
