<?php

use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

include_once __DIR__ . '/index.php';

Route::get('/virtfusion/login/{id}', function (Request $request, OrderProduct $id) {
    return VirtFusion_login($id, $request);
})->name('extensions.virtfusion.login');