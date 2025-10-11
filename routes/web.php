<?php

use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Middleware\MustVerfiyEmail;
use App\Livewire\Auth;
use App\Livewire\Cart;
use App\Livewire\Client;
use App\Livewire\Dashboard;
use App\Livewire\Home;
use App\Livewire\Invoices;
use App\Livewire\Products;
use App\Livewire\Services;
use App\Livewire\Tickets;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

// Destroy the session and log out the user.
// auth()->logout();
// Authorization routes
Route::group(['middleware' => ['web', 'guest']], function () {
    Route::get('/login', Auth\Login::class)->name('login');
    Route::get('/2fa', Auth\Tfa::class)->name('2fa');
    Route::get('/register', Auth\Register::class)->name('register');
    // Todo
    Route::get('/password/request', Auth\Password\Request::class)->name('password.request');
    Route::get('/password/reset/{token}', Auth\Password\Reset::class)->name('password.reset');

    Route::get('/oauth/{provider}', [SocialLoginController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/oauth/{provider}/callback', [SocialLoginController::class, 'handle'])->name('oauth.handle');
});

Route::group(['middleware' => ['web', 'auth', MustVerfiyEmail::class]], function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');

    Route::get('/invoices', Invoices\Index::class)->name('invoices');
    Route::get('/invoices/{invoice}', Invoices\Show::class)->name('invoices.show')->middleware('can:view,invoice');

    Route::get('/tickets', Tickets\Index::class)->name('tickets');
    Route::get('/tickets/create', Tickets\Create::class)->name('tickets.create');
    Route::get('/tickets/{ticket}', Tickets\Show::class)->name('tickets.show')->middleware('can:view,ticket');

    Route::get('/services', Services\Index::class)->name('services');
    Route::get('/services/{service}', Services\Show::class)->name('services.show')->middleware('can:view,service');
    Route::get('/services/{service}/upgrade', Services\Upgrade::class)->name('services.upgrade')->middleware('can:view,service');
});

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/account', Client\Account::class)->name('account');
    Route::get('/account/security', Client\Security::class)->name('account.security');
    Route::get('/account/credits', Client\Credits::class)->name('account.credits');
    Route::get('/account/payment-methods', Client\PaymentMethods::class)->name('account.payment-methods');
    Route::get('/account/notifications', Client\Notifications::class)->name('account.notifications');

    Route::get('/email/verify', Auth\VerifyEmail::class)->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard');
    })->middleware(['signed'])->name('verification.verify');
    Route::get('/tickets/attachments/{attachment:uuid}', [TicketAttachmentController::class, 'download'])->name('tickets.attachments.show')->middleware('can:view,attachment');
});

Route::get('cart', Cart::class)->name('cart')->middleware('checkout');

Route::group(['prefix' => 'products', 'middleware' => 'checkout'], function () {
    Route::get('/{category:slug}', Products\Index::class)->name('category.show')/* ->where('category', '[A-Za-z0-9_/-]+') */;
    Route::get('/{category:slug}/{product:slug}', Products\Show::class)->name('products.show')/* ->where('category', '[A-Za-z0-9_/-]+') */;
    Route::get('/{category:slug}/{product:slug}/checkout', Products\Checkout::class)->name('products.checkout')/* ->where('category', '[A-Za-z0-9_/-]+') */;
    // Allow for nested categories
});

Route::group([
    'as' => 'passport.',
    'prefix' => config('passport.path', 'oauth'),
    'namespace' => '\Laravel\Passport\Http\Controllers',
], function () {
    Route::get('/oauth/authorize', [
        'uses' => 'Laravel\Passport\Http\Controllers\AuthorizationController@authorize',
        'as' => 'x.authorize',
        'middleware' => 'web',
    ]);
});
