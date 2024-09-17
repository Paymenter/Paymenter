<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('/oauth/token', [
    'uses' => 'Laravel\Passport\Http\Controllers\AccessTokenController@issueToken',
    'as' => 'token',
    'middleware' => 'throttle',
]);

Route::get('/me', function () {
    return response()->json(Auth::user());
})->middleware(['auth:api', 'scope:profile']);
