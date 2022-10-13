<?php

use Illuminate\Support\Facades\Route;
include(__DIR__ . '/index.php');

Route::get('/stripe', function () {
    $url = create(request());
    return redirect($url->url, 303);
});

Route::post('/stripe/webhook', function () {
    webhook(request());
});