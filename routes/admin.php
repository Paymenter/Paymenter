<?php

use Illuminate\Support\Facades\Route;

// admin routes;
Route::group(['prefix' => 'admin'], function () {
    Route::get('/', [App\Http\Controllers\Admin\MainController::class, 'index'])->middleware(['auth.admin'])->name('admin.index');

    Route::group(['prefix' => 'tickets'], function () {
        Route::get('/', [App\Http\Controllers\Admin\TicketController::class, 'index'])->middleware(['auth.admin'])->name('admin.tickets');
        Route::get('/create', [App\Http\Controllers\Admin\TicketController::class, 'create'])->middleware(['auth.admin'])->name('admin.tickets.create');
        Route::post('/create', [App\Http\Controllers\Admin\TicketController::class, 'store'])->middleware(['auth.admin'])->name('admin.tickets.store');
        Route::get('/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->middleware(['auth.admin'])->name('admin.tickets.show');
        Route::post('/{ticket}/status', [App\Http\Controllers\Admin\TicketController::class, 'status'])->middleware(['auth.admin'])->name('admin.tickets.status');
        Route::post('/{ticket}/reply', [App\Http\Controllers\Admin\TicketController::class, 'reply'])->middleware(['auth.admin'])->name('admin.tickets.reply');
    });

    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingController::class, 'index'])->middleware(['auth.admin', 'password.confirm'])->name('admin.settings');
        Route::post('/general', [App\Http\Controllers\Admin\SettingController::class, 'general'])->middleware(['auth.admin', 'password.confirm'])->name('admin.settings.general');
        Route::post('/email', [App\Http\Controllers\Admin\SettingController::class, 'email'])->middleware(['auth.admin', 'password.confirm'])->name('admin.settings.email');
        Route::post('/email/test', [App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->middleware(['auth.admin', 'password.confirm'])->name('admin.settings.email.test');
        Route::post('/login', [App\Http\Controllers\Admin\SettingController::class, 'login'])->middleware(['auth.admin', 'password.confirm'])->name('admin.settings.login');
        Route::post('/security', [App\Http\Controllers\Admin\SettingController::class, 'security'])->middleware(['auth.admin', 'password.confirm'])->name('admin.settings.security');
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [App\Http\Controllers\Admin\ProductController::class, 'index'])->middleware(['auth.admin'])->name('admin.products');
        Route::get('/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->middleware(['auth.admin'])->name('admin.products.create');
        Route::post('/create', [App\Http\Controllers\Admin\ProductController::class, 'store'])->middleware(['auth.admin'])->name('admin.products.store');
        Route::get('/{product}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->middleware(['auth.admin'])->name('admin.products.edit');
        Route::post('/{product}/edit', [App\Http\Controllers\Admin\ProductController::class, 'update'])->middleware(['auth.admin'])->name('admin.products.update');
        Route::get('/{product}/pricing', [App\Http\Controllers\Admin\ProductController::class, 'pricing'])->middleware(['auth.admin'])->name('admin.products.pricing');
        Route::post('/{product}/pricing', [App\Http\Controllers\Admin\ProductController::class, 'pricingUpdate'])->middleware(['auth.admin'])->name('admin.products.pricing.update');
        Route::get('/{product}/extension', [App\Http\Controllers\Admin\ProductController::class, 'extension'])->middleware(['auth.admin'])->name('admin.products.extension');
        Route::post('/{product}/extension', [App\Http\Controllers\Admin\ProductController::class, 'extensionUpdate'])->middleware(['auth.admin'])->name('admin.products.extension.update');
        Route::get('/{product}/extension/export', [App\Http\Controllers\Admin\ProductController::class, 'extensionExport'])->middleware(['auth.admin'])->name('admin.products.extension.export');
        Route::post('/{product}/extension/import', [App\Http\Controllers\Admin\ProductController::class, 'extensionImport'])->middleware(['auth.admin'])->name('admin.products.extension.import');
        Route::post('/{product}/duplicate', [App\Http\Controllers\Admin\ProductController::class, 'duplicate'])->middleware(['auth.admin'])->name('admin.products.duplicate');
        Route::delete('/{product}/delete', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->middleware(['auth.admin'])->name('admin.products.destroy');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->middleware(['auth.admin'])->name('admin.categories');
        Route::get('/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->middleware(['auth.admin'])->name('admin.categories.create');
        Route::post('/create', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->middleware(['auth.admin'])->name('admin.categories.store');
        Route::get('/{category}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->middleware(['auth.admin'])->name('admin.categories.edit');
        Route::post('/{category}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->middleware(['auth.admin'])->name('admin.categories.update');
        Route::delete('/{category}/delete', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->middleware(['auth.admin'])->name('admin.categories.delete');
    });

    Route::group(['prefix' => 'extensions'], function () {
        Route::get('/', [App\Http\Controllers\Admin\ExtensionController::class, 'index'])->middleware(['auth.admin', 'password.confirm'])->name('admin.extensions');
        Route::post('/download', [App\Http\Controllers\Admin\ExtensionController::class, 'download'])->middleware(['auth.admin', 'password.confirm'])->name('admin.extensions.download');
        Route::get('/edit/{sort}/{name}', [App\Http\Controllers\Admin\ExtensionController::class, 'edit'])->middleware(['auth.admin', 'password.confirm'])->name('admin.extensions.edit');
        Route::post('/edit/{sort}/{name}', [App\Http\Controllers\Admin\ExtensionController::class, 'update'])->middleware(['auth.admin', 'password.confirm'])->name('admin.extensions.update');
    });

    Route::group(['prefix' => 'clients'], function () {
        Route::get('/', [App\Http\Controllers\Admin\ClientController::class, 'index'])->middleware(['auth.admin'])->name('admin.clients');
        Route::get('/create', [App\Http\Controllers\Admin\ClientController::class, 'create'])->middleware(['auth.admin'])->name('admin.clients.create');
        Route::post('/create', [App\Http\Controllers\Admin\ClientController::class, 'store'])->middleware(['auth.admin'])->name('admin.clients.store');
        Route::get('/{user}/login', [App\Http\Controllers\Admin\ClientController::class, 'loginasClient'])->middleware(['auth.admin'])->name('admin.clients.loginasclient');
        Route::get('/{user}/edit', [App\Http\Controllers\Admin\ClientController::class, 'edit'])->middleware(['auth.admin'])->name('admin.clients.edit');
        Route::post('/{user}/edit', [App\Http\Controllers\Admin\ClientController::class, 'update'])->middleware(['auth.admin'])->name('admin.clients.update');
        Route::delete('/{user}/delete', [App\Http\Controllers\Admin\ClientController::class, 'destroy'])->middleware(['auth.admin'])->name('admin.clients.delete');
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::get('/', [App\Http\Controllers\Admin\OrderController::class, 'index'])->middleware(['auth.admin'])->name('admin.orders');
        Route::delete('/{order}/delete', [App\Http\Controllers\Admin\OrderController::class, 'destroy'])->middleware(['auth.admin'])->name('admin.orders.delete');
        Route::get('/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->middleware(['auth.admin'])->name('admin.orders.show');
        Route::post('/{order}/{product}/change', [App\Http\Controllers\Admin\OrderController::class, 'changeProduct'])->middleware(['auth.admin'])->name('admin.orders.changeProduct');
        Route::post('/{order}/unsuspend', [App\Http\Controllers\Admin\OrderController::class, 'unsuspend'])->middleware(['auth.admin'])->name('admin.orders.unsuspend');
        Route::post('/{order}/suspend', [App\Http\Controllers\Admin\OrderController::class, 'suspend'])->middleware(['auth.admin'])->name('admin.orders.suspend');
        Route::post('/{order}/create', [App\Http\Controllers\Admin\OrderController::class, 'create'])->middleware(['auth.admin'])->name('admin.orders.create');
        Route::post('/{order}/paid', [App\Http\Controllers\Admin\OrderController::class, 'paid'])->middleware(['auth.admin'])->name('admin.orders.paid');
    });

    Route::group(['prefix' => 'invoices'], function () {
        Route::get('/', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->middleware(['auth.admin'])->name('admin.invoices');
        Route::get('/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'show'])->middleware(['auth.admin'])->name('admin.invoices.show');
        Route::post('/{invoice}/paid', [App\Http\Controllers\Admin\InvoiceController::class, 'paid'])->middleware(['auth.admin'])->name('admin.invoices.paid');
    });

    Route::group(['prefix' => 'coupon'], function() {
        Route::get('/', [App\Http\Controllers\Admin\CouponController::class, 'index'])->middleware(['auth.admin'])->name('admin.coupons');
        Route::get('/create', [App\Http\Controllers\Admin\CouponController::class, 'create'])->middleware(['auth.admin'])->name('admin.coupons.create');
        Route::post('/create', [App\Http\Controllers\Admin\CouponController::class, 'store'])->middleware(['auth.admin'])->name('admin.coupons.store');
        Route::get('/{coupon}/edit', [App\Http\Controllers\Admin\CouponController::class, 'edit'])->middleware(['auth.admin'])->name('admin.coupons.edit');
        Route::put('/{coupon}/edit', [App\Http\Controllers\Admin\CouponController::class, 'update'])->middleware(['auth.admin'])->name('admin.coupons.update');
        Route::delete('/{coupon}/delete', [App\Http\Controllers\Admin\CouponController::class, 'destroy'])->middleware(['auth.admin'])->name('admin.coupons.delete');
    });

    Route::group(['prefix' => 'announcements'], function() {
        Route::get('/', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->middleware(['auth.admin'])->name('admin.announcements');
        Route::get('/create', [App\Http\Controllers\Admin\AnnouncementController::class, 'create'])->middleware(['auth.admin'])->name('admin.announcements.create');
        Route::post('/create', [App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->middleware(['auth.admin'])->name('admin.announcements.store');
        Route::get('/{announcement}/edit', [App\Http\Controllers\Admin\AnnouncementController::class, 'edit'])->middleware(['auth.admin'])->name('admin.announcements.edit');
        Route::post('/{announcement}/edit', [App\Http\Controllers\Admin\AnnouncementController::class, 'update'])->middleware(['auth.admin'])->name('admin.announcements.update');
        Route::delete('/{announcement}/delete', [App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->middleware(['auth.admin'])->name('admin.announcements.delete');
    });
});
