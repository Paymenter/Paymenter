<?php

use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\CreditController;
use App\Http\Controllers\Api\Admin\InvoiceController;
use App\Http\Controllers\Api\Admin\InvoiceItemController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\ServiceController;
use App\Http\Controllers\Api\Admin\TicketController;
use App\Http\Controllers\Api\Admin\TicketMessageController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/oauth/token', [
    'uses' => 'Laravel\Passport\Http\Controllers\AccessTokenController@issueToken',
    'as' => 'token',
    'middleware' => 'throttle',
]);

Route::get('/me', [ProfileController::class, 'me'])->middleware(['auth:api', 'scope:profile']);

Route::group(['middleware' => ['api.admin'], 'prefix' => 'v1/admin', 'as' => 'api.v1.admin.'], function () {
    Route::apiResources([
        'categories' => CategoryController::class,
        'credits' => CreditController::class,
        'users' => UserController::class,
        'products' => ProductController::class,
        'services' => ServiceController::class,
        'orders' => OrderController::class,
        'invoices' => InvoiceController::class,
        'invoice-items' => InvoiceItemController::class,
        'tickets' => TicketController::class,
        'ticket-messages' => TicketMessageController::class,
    ]);
});
