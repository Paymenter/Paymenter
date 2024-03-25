<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;

Route::get('/', function () {
    return view('welcome');
});


// Destroy the session and log out the user.
//auth()->logout();
// Authorization routes 
Route::group(['middleware' => ['web', 'guest']], function () {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
    
    // Todo
    Route::get('password/reset')->name('password.reset');
    Route::get('/oauth/{provider}')->name('oauth');
});
