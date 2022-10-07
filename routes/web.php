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

$theme = 'default';

Route::get('/', function () {
    $theme = 'default';

    return view($theme . '.welcome');
});
// auth routes;

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
