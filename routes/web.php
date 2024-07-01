<?php

use App\Http\Controllers\SocialLoginController;
use App\Livewire\Auth;
use App\Livewire\Cart;
use App\Livewire\Products;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

// Destroy the session and log out the user.
//auth()->logout();
// Authorization routes
Route::group(['middleware' => ['web', 'guest']], function () {
    Route::get('login', Auth\Login::class)->name('login');
    Route::get('register', Auth\Register::class)->name('register');
    // Todo
    Route::get('password/reset')->name('password.reset');
    Route::get('/oauth/{provider}', [SocialLoginController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/oauth/{provider}/callback', [SocialLoginController::class, 'handle'])->name('oauth.handle');
});

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    //Route::get('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('profile', function () {
        return view('profile');
    })->name('profile');
});

Route::get('cart', Cart::class)->name('cart');

Route::group(['prefix' => 'products'], function () {
    Route::get('/{category:slug}', Products\Index::class)->name('category.show')/*->where('category', '[A-Za-z0-9_/-]+')*/;
    Route::get('/{category:slug}/{product}', Products\Show::class)->name('products.show')/*->where('category', '[A-Za-z0-9_/-]+')*/;
    Route::get('/{category:slug}/{product}/checkout', Products\Checkout::class)->name('products.checkout')/*->where('category', '[A-Za-z0-9_/-]+')*/;
    // Allow for nested categories
});
