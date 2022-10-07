<?php

use Illuminate\Support\Facades\Route;

// admin routes;
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [App\Http\Controllers\Admin\MainController::class, 'index'])->middleware(['auth.admin'])->name('admin');
    //Route::get('/admin', [App\Http\Controllers\AdminController::class, 'index'])->name('admin');
    Route::group(['prefix' => 'tickets'], function() {
        Route::get('/', [App\Http\Controllers\Admin\TicketsController::class, 'index'])->middleware(['auth.admin'])->name('admin.tickets');
        Route::get('/{id}', [App\Http\Controllers\Admin\TicketsController::class, 'show'])->middleware(['auth.admin'])->name('admin.tickets.show');
        
        Route::get('/{id}/edit', [App\Http\Controllers\Admin\TicketsController::class, 'edit'])->middleware(['auth.admin'])->name('admin.tickets.edit');
        Route::post('/{id}/edit', [App\Http\Controllers\Admin\TicketsController::class, 'update'])->middleware(['auth.admin'])->name('admin.tickets.update');
        Route::get('/{id}/delete', [App\Http\Controllers\Admin\TicketsController::class, 'destroy'])->middleware(['auth.admin'])->name('admin.tickets.delete');
    });
});
   