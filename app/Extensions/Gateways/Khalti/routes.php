<?php

use Illuminate\Support\Facades\Route;

include_once __DIR__ . '/index.php';

Route::get('/khalti/callback/{invoiceId}', function($invoiceId) {
    return Khalti_callback($invoiceId, request());
})->name('khalti.callback');