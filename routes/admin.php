<?php

use Illuminate\Support\Facades\Route;

// admin routes;
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [App\Http\Controllers\Admin\MainController::class, 'index'])->middleware(['auth.admin'])->name('admin');
    //Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin');
});
   