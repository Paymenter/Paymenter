<?php

use Illuminate\Support\Facades\Route;
include(__DIR__ . '/index.php');

Route::get('/stripe/webhook', function () {
    $url = create(request());
    error_log($url);
    return redirect($url->url, 303);
});