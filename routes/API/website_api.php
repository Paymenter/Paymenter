<?php

use App\Http\Controllers\API\Website\AnnouncementController;
use App\Http\Controllers\API\Website\CategoryController;
use App\Http\Controllers\API\Website\ProductController;
use Illuminate\Support\Facades\Route;

// Products
Route::group(['prefix' => 'v1/products'], function () {
    Route::get('/', [ProductController::class, 'getProducts'])->name('api.website.v1.products.getProducts');
});

Route::group(['prefix' => 'v1/categories'], function () {
    Route::get('/', [CategoryController::class, 'getCategories'])->name('api.website.v1.products.getCategories');
});

// Annoucements
Route::group(['prefix' => 'v1/announcements'], function () {
    Route::get('/', [AnnouncementController::class, 'getAnnouncements'])->name('api.website.v1.products.getAnnouncements');
    Route::get('/{id}', [AnnouncementController::class, 'getAnnouncement'])->name('api.website.v1.products.getAnnouncement');
});
