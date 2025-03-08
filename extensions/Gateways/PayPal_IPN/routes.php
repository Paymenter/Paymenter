<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\PayPal_IPN\PayPal_IPN;

Route::post('/extensions/paypal_ipn/notify', [PayPal_IPN::class, 'notify'])->name('extensions.gateways.paypal_ipn.notify');
