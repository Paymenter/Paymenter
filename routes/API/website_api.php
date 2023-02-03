<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Website\ProductController;

// Products
Route::group(['prefix' => 'v1/products'], function () {
    Route::get('/', [ProductController::class, 'getProducts'])->name('api.website.v1.products.getProducts');
});
