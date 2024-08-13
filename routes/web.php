<?php

use App\Http\Controllers\SocialLoginController;
use App\Livewire\Auth;
use App\Livewire\Cart;
use App\Livewire\Clients;
use App\Livewire\Dashboard;
use App\Livewire\Invoice;
use App\Livewire\Products;
use App\Livewire\Tickets;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Destroy the session and log out the user.
//auth()->logout();
// Authorization routes
Route::group(['middleware' => ['web', 'guest']], function () {
    Route::get('login', Auth\Login::class)->name('login');
    Route::get('2fa', Auth\Tfa::class)->name('2fa');
    Route::get('register', Auth\Register::class)->name('register');
    // Todo
    Route::get('password/reset')->name('password.reset');
    Route::get('/oauth/{provider}', [SocialLoginController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/oauth/{provider}/callback', [SocialLoginController::class, 'handle'])->name('oauth.handle');
});

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::get('account', Clients\Account::class)->name('account');
    Route::get('account/security', Clients\Security::class)->name('account.security');

    Route::get('invoices/{invoice}', Invoice\Show::class)->name('invoices.show');

    Route::get('tickets', Tickets\Index::class)->name('tickets');
    Route::get('tickets/{ticket}', Tickets\Show::class)->name('tickets.show');

});

Route::get('cart', Cart::class)->name('cart');

Route::group(['prefix' => 'products'], function () {
    Route::get('/{category:slug}', Products\Index::class)->name('category.show')/*->where('category', '[A-Za-z0-9_/-]+')*/;
    Route::get('/{category:slug}/{product:slug}', Products\Show::class)->name('products.show')/*->where('category', '[A-Za-z0-9_/-]+')*/;
    Route::get('/{category:slug}/{product:slug}/checkout', Products\Checkout::class)->name('products.checkout')/*->where('category', '[A-Za-z0-9_/-]+')*/;
    // Allow for nested categories
});

include_once 'extensions.php';
