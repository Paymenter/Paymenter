<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/', [App\Http\Controllers\BasisController::class, 'index'])->name('index');
// auth routes;
Route::get('/home', function () {
    return view('home');
})->middleware(['auth'])->name('home');
// return homecontroller;
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->middleware(['auth'])->name('home');
Route::get('/manifest.json', [App\Http\Controllers\HomeController::class, 'manifest'])->name('manifest');
Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile')->middleware(['auth', 'password.confirm']);
Route::post('/profile', [App\Http\Controllers\HomeController::class, 'update'])->name('profile.update')->middleware(['auth', 'password.confirm']);
Route::get('/products', [App\Http\Controllers\BasisController::class, 'products'])->name('products');

Route::group(['prefix'=>'checkout'], function(){
    Route::get('/', [App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/', [App\Http\Controllers\CheckoutController::class, 'pay'])->name('checkout.pay')->middleware('auth');
    Route::post('/{id}', [App\Http\Controllers\CheckoutController::class, 'remove'])->name('checkout.remove');
    Route::post('/{id}/update', [App\Http\Controllers\CheckoutController::class, 'update'])->name('checkout.update');
    Route::get('/add', [App\Http\Controllers\CheckoutController::class, 'add'])->name('checkout.add');
    Route::get('/success', [App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/cancel', [App\Http\Controllers\CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

Route::group(['prefix'=>'tickets'], function(){
    Route::get('/', [App\Http\Controllers\TicketsController::class, 'index'])->name('tickets.index');
    Route::get('/create', [App\Http\Controllers\TicketsController::class, 'create'])->name('tickets.create');
    Route::post('/store', [App\Http\Controllers\TicketsController::class, 'store'])->name('tickets.store');
    Route::get('/{id}', [App\Http\Controllers\TicketsController::class, 'show'])->name('tickets.show');
    Route::post('/{id}/update', [App\Http\Controllers\TicketsController::class, 'update'])->name('tickets.update');
    Route::post('{id}/reply', [App\Http\Controllers\TicketsController::class, 'reply'])->name('tickets.reply');
    Route::delete('/{id}/delete', [App\Http\Controllers\TicketsController::class, 'delete'])->name('tickets.delete');
});

Route::group(['prefix' => 'invoices'], function(){
    Route::get('/', [App\Http\Controllers\InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('/{id}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoice.show');
    Route::post('/{id}/pay', [App\Http\Controllers\InvoiceController::class, 'pay'])->name('invoice.pay');
    Route::get('/{id}/download', [App\Http\Controllers\InvoiceController::class, 'download'])->name('invoice.download');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/extensions.php';