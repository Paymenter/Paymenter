<?php

use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/oauth/token', [
    'uses' => 'Laravel\Passport\Http\Controllers\AccessTokenController@issueToken',
    'as' => 'token',
    'middleware' => 'throttle',
]);

Route::get('/me', [ProfileController::class, 'me'])->middleware(['auth:api', 'scope:profile']);


Route::group(['middleware' => ['auth:api'], 'prefix' => 'v1/admin'], function () {
    Route::apiResource('users', UserController::class);
});
