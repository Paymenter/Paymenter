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
});
   