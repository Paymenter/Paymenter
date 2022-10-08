<?php

use Illuminate\Support\Facades\Route;

// admin routes;
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [App\Http\Controllers\Admin\MainController::class, 'index'])->middleware(['auth.admin'])->name('admin');

    Route::group(['prefix' => 'tickets'], function() {
        Route::get('/', [App\Http\Controllers\Admin\TicketsController::class, 'index'])->middleware(['auth.admin'])->name('admin.tickets');
        Route::get('/{id}', [App\Http\Controllers\Admin\TicketsController::class, 'show'])->middleware(['auth.admin'])->name('admin.tickets.show');
        Route::post('/{id}/status', [App\Http\Controllers\Admin\TicketsController::class, 'status'])->middleware(['auth.admin'])->name('admin.tickets.status');
        Route::post('/{id}/reply', [App\Http\Controllers\Admin\TicketsController::class, 'reply'])->middleware(['auth.admin'])->name('admin.tickets.reply');
    });

    Route::group(['prefix' => 'settings'], function() {
        Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->middleware(['auth.admin'])->name('admin.settings');
        Route::post('/', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->middleware(['auth.admin'])->name('admin.settings.update');
    });

    Route::group(['prefix'=> 'products'], function(){
        Route::get('/', [App\Http\Controllers\Admin\ProductsController::class, 'index'])->middleware(['auth.admin'])->name('admin.products');
        Route::get('/create', [App\Http\Controllers\Admin\ProductsController::class, 'create'])->middleware(['auth.admin'])->name('admin.products.create');
        Route::post('/create', [App\Http\Controllers\Admin\ProductsController::class, 'store'])->middleware(['auth.admin'])->name('admin.products.store');
        Route::get('/{id}', [App\Http\Controllers\Admin\ProductsController::class, 'show'])->middleware(['auth.admin'])->name('admin.products.show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\ProductsController::class, 'edit'])->middleware(['auth.admin'])->name('admin.products.edit');
        Route::post('/{id}/edit', [App\Http\Controllers\Admin\ProductsController::class, 'update'])->middleware(['auth.admin'])->name('admin.products.update');
        Route::post('/{id}/delete', [App\Http\Controllers\Admin\ProductsController::class, 'destroy'])->middleware(['auth.admin'])->name('admin.products.delete');
    });

    Route::group(['prefix'=> 'category'], function(){
        Route::get('/', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->middleware(['auth.admin'])->name('admin.category');
        Route::get('/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->middleware(['auth.admin'])->name('admin.category.create');
        Route::post('/create', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->middleware(['auth.admin'])->name('admin.category.store');
        Route::get('/{id}', [App\Http\Controllers\Admin\CategoryController::class, 'show'])->middleware(['auth.admin'])->name('admin.category.show');
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->middleware(['auth.admin'])->name('admin.category.edit');
        Route::post('/{id}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->middleware(['auth.admin'])->name('admin.category.update');
        Route::post('/{id}/delete', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->middleware(['auth.admin'])->name('admin.category.delete');
    });
});
   