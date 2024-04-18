<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Livewire\Auth;
use App\Livewire\Admin;

Route::get('/', function () {
    return view('home');
});


// Destroy the session and log out the user.
//auth()->logout();
// Authorization routes 
Route::group(['middleware' => ['web', 'guest']], function () {
    Route::get('login', Auth\Login::class)->name('login');
    Route::get('register', Auth\Register::class)->name('register');
    // Todo
    Route::get('password/reset')->name('password.reset');
    Route::get('/oauth/{provider}')->name('oauth');
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

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', Admin\Index::class)->name('index')->middleware('has:admin.dashboard.view');
    Route::get('settings', Admin\Configuration\Settings::class)->name('settings')->middleware('has:admin.settings.view');
    Route::get('health', Admin\Configuration\Health::class)->name('health')->middleware('has:admin.health.view');

    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', Admin\Users\Index::class)->name('index')->middleware('has:admin.users.view');
        // Route::get('create', Admin\Users\Create::class)->name('create')->middleware('has:admin.users.create');
        Route::get('{user}', Admin\Users\Show::class)->name('show')->middleware('has:admin.users.view');
        Route::get('{user}/edit', Admin\Users\Edit::class)->name('edit')->middleware('has:admin.users.edit');
    });
});
