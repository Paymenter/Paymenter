<?php

use Illuminate\Support\Facades\Route;


Route::post('/xendit/webhook', [App\Extensions\Gateways\Xendit\Xendit::class, 'webhook'])->name('xendit.webhook');
