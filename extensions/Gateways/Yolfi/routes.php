<?php

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Gateways\Yolfi\Yolfi;

Route::post('/extensions/gateways/yolfi/webhook', [Yolfi::class, 'webhook'])
    ->withoutMiddleware([PreventRequestForgery::class])
    ->name('extensions.gateways.yolfi.webhook');
