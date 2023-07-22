<?php

use Illuminate\Support\Facades\Route;

include_once __DIR__ . '/index.php';

Route::post('/xendit/webhook', function () {
    return Xendit_webhook(request());
})->name('xendit.webhook');
