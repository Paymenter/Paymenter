<?php

use Illuminate\Support\Facades\Route;

// admin routes;
Route::group(['prefix' => 'admin', 'middleware' => 'permission:ADMINISTRATOR'], function () {
    Route::get('/', [App\Http\Controllers\Admin\MainController::class, 'index'])->name('admin.index');

    Route::group(['prefix' => 'tickets', 'middleware' => 'permission:VIEW_TICKETS'], function () {
        Route::get('/', [App\Http\Controllers\Admin\TicketController::class, 'index'])->name('admin.tickets');
        Route::get('/create', [App\Http\Controllers\Admin\TicketController::class, 'create'])->middleware(['permission:CREATE_TICKETS'])->name('admin.tickets.create');
        Route::post('/create', [App\Http\Controllers\Admin\TicketController::class, 'store'])->middleware(['permission:CREATE_TICKETS'])->name('admin.tickets.store');
        Route::get('/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'show'])->middleware(['permission:VIEW_TICKETS'])->name('admin.tickets.show');
        Route::post('/{ticket}/update', [App\Http\Controllers\Admin\TicketController::class, 'update'])->middleware(['permission:EDIT_TICKETS'])->name('admin.tickets.update');
        Route::post('/{ticket}/reply', [App\Http\Controllers\Admin\TicketController::class, 'reply'])->middleware(['permission:EDIT_TICKETS'])->name('admin.tickets.reply');
    });

    Route::group(['prefix' => 'settings', 'middleware' => 'permission:VIEW_SETTINGS'], function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingController::class, 'index'])->middleware(['password.confirm', 'permission:VIEW_SETTINGS'])->name('admin.settings');
        Route::post('/general', [App\Http\Controllers\Admin\SettingController::class, 'general'])->middleware(['password.confirm', 'permission:EDIT_SETTINGS'])->name('admin.settings.general');
        Route::post('/company', [App\Http\Controllers\Admin\SettingController::class, 'company'])->middleware(['password.confirm', 'permission:EDIT_SETTINGS'])->name('admin.settings.company');
        Route::post('/email', [App\Http\Controllers\Admin\SettingController::class, 'email'])->middleware(['password.confirm', 'permission:EDIT_SETTINGS'])->name('admin.settings.email');
        Route::post('/email/test', [App\Http\Controllers\Admin\SettingController::class, 'testEmail'])->middleware(['password.confirm', 'permission:EDIT_SETTINGS'])->name('admin.settings.email.test');
        Route::post('/login', [App\Http\Controllers\Admin\SettingController::class, 'login'])->middleware(['password.confirm', 'permission:EDIT_SETTINGS'])->name('admin.settings.login');
        Route::post('/security', [App\Http\Controllers\Admin\SettingController::class, 'security'])->middleware(['password.confirm', 'permission:EDIT_SETTINGS'])->name('admin.settings.security');
        Route::post('/theme', [App\Http\Controllers\Admin\SettingController::class, 'theme'])->middleware(['password.confirm', 'permission:EDIT_SETTINGS'])->name('admin.settings.theme');
        Route::post('/credits', [App\Http\Controllers\Admin\SettingController::class, 'credits'])->middleware(['password.confirm', 'permission:EDIT_SETTINGS'])->name('admin.settings.credits');
        Route::post('/affiliate', [App\Http\Controllers\Admin\SettingController::class, 'affiliate'])->middleware(['password.confirm', 'permission:EDIT_SETTINGS'])->name('admin.settings.affiliate');
    });

    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [App\Http\Controllers\Admin\ProductController::class, 'index'])->middleware(['permission:VIEW_PRODUCTS'])->name('admin.products');
        Route::post('/', [App\Http\Controllers\Admin\ProductController::class, 'reorder'])->middleware(['permission:EDIT_PRODUCTS'])->name('admin.products.reorder');
        Route::get('/create', [App\Http\Controllers\Admin\ProductController::class, 'create'])->middleware(['permission:CREATE_PRODUCTS'])->name('admin.products.create');
        Route::post('/create', [App\Http\Controllers\Admin\ProductController::class, 'store'])->middleware(['permission:CREATE_PRODUCTS'])->name('admin.products.store');
        Route::get('/{product}/edit', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->middleware(['permission:VIEW_PRODUCTS'])->name('admin.products.edit');
        Route::post('/{product}/edit', [App\Http\Controllers\Admin\ProductController::class, 'update'])->middleware(['permission:EDIT_PRODUCTS'])->name('admin.products.update');
        Route::get('/{product}/pricing', [App\Http\Controllers\Admin\ProductController::class, 'pricing'])->middleware(['permission:VIEW_PRODUCTS'])->name('admin.products.pricing');
        Route::post('/{product}/pricing', [App\Http\Controllers\Admin\ProductController::class, 'pricingUpdate'])->middleware(['permission:EDIT_PRODUCTS'])->name('admin.products.pricing.update');
        Route::get('/{product}/extension', [App\Http\Controllers\Admin\ProductController::class, 'extension'])->middleware(['permission:VIEW_PRODUCTS'])->name('admin.products.extension');
        Route::post('/{product}/extension', [App\Http\Controllers\Admin\ProductController::class, 'extensionUpdate'])->middleware(['permission:EDIT_PRODUCTS'])->name('admin.products.extension.update');
        Route::get('/{product}/extension/export', [App\Http\Controllers\Admin\ProductController::class, 'extensionExport'])->middleware(['permission:EDIT_PRODUCTS'])->name('admin.products.extension.export');
        Route::post('/{product}/extension/import', [App\Http\Controllers\Admin\ProductController::class, 'extensionImport'])->middleware(['permission:EDIT_PRODUCTS'])->name('admin.products.extension.import');
        Route::post('/{product}/duplicate', [App\Http\Controllers\Admin\ProductController::class, 'duplicate'])->middleware(['permission:CREATE_PRODUCTS'])->name('admin.products.duplicate');
        Route::get('/{product}/upgrades', [App\Http\Controllers\Admin\ProductController::class, 'upgrade'])->middleware(['permission:VIEW_PRODUCTS'])->name('admin.products.upgrade');
        Route::post('/{product}/upgrades', [App\Http\Controllers\Admin\ProductController::class, 'upgradeUpdate'])->middleware(['permission:EDIT_PRODUCTS'])->name('admin.products.upgrade.update');
        Route::delete('/{product}/delete', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->middleware(['permission:DELETE_PRODUCTS'])->name('admin.products.destroy');
    });

    Route::group(['prefix' => 'categories'], function () {
        Route::get('/', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->middleware(['permission:VIEW_CATEGORIES'])->name('admin.categories');
        Route::post('/', [App\Http\Controllers\Admin\CategoryController::class, 'reorder'])->middleware(['permission:EDIT_CATEGORIES'])->name('admin.categories.reorder');
        Route::get('/create', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->middleware(['permission:CREATE_CATEGORIES'])->name('admin.categories.create');
        Route::post('/create', [App\Http\Controllers\Admin\CategoryController::class, 'store'])->middleware(['permission:CREATE_CATEGORIES'])->name('admin.categories.store');
        Route::get('/{category}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->middleware(['permission:VIEW_CATEGORIES'])->name('admin.categories.edit');
        Route::post('/{category}/edit', [App\Http\Controllers\Admin\CategoryController::class, 'update'])->middleware(['permission:EDIT_CATEGORIES'])->name('admin.categories.update');
        Route::delete('/{category}/delete', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->middleware(['permission:DELETE_CATEGORIES'])->name('admin.categories.delete');
    });

    Route::group(['prefix' => 'extensions'], function () {
        Route::get('/', [App\Http\Controllers\Admin\ExtensionController::class, 'index'])->middleware(['password.confirm', 'permission:VIEW_EXTENSIONS'])->name('admin.extensions');
        Route::get('/browse', [App\Http\Controllers\Admin\ExtensionController::class, 'browse'])->middleware(['password.confirm', 'permission:VIEW_EXTENSIONS'])->name('admin.extensions.browse');
        Route::post('/install/{id}', [App\Http\Controllers\Admin\ExtensionController::class, 'install'])->middleware(['password.confirm', 'permission:EDIT_EXTENSIONS'])->name('admin.extensions.install');
        Route::post('/download', [App\Http\Controllers\Admin\ExtensionController::class, 'download'])->middleware(['password.confirm', 'permission:EDIT_EXTENSIONS'])->name('admin.extensions.download');
        Route::get('/edit/{sort}/{name}', [App\Http\Controllers\Admin\ExtensionController::class, 'edit'])->middleware(['password.confirm', 'permission:VIEW_EXTENSIONS'])->name('admin.extensions.edit');
        Route::post('/edit/{sort}/{name}', [App\Http\Controllers\Admin\ExtensionController::class, 'update'])->middleware(['password.confirm', 'permission:EDIT_EXTENSIONS'])->name('admin.extensions.update');
        Route::post('/update/{extension}', [App\Http\Controllers\Admin\ExtensionController::class, 'updateExtension'])->middleware(['password.confirm', 'permission:EDIT_EXTENSIONS'])->name('admin.extensions.updateExtension');
    });

    Route::group(['prefix' => 'clients'], function () {
        Route::get('/', [App\Http\Controllers\Admin\ClientController::class, 'index'])->middleware(['permission:VIEW_CLIENTS'])->name('admin.clients');
        Route::get('/create', [App\Http\Controllers\Admin\ClientController::class, 'create'])->middleware(['permission:CREATE_CLIENTS'])->name('admin.clients.create');
        Route::post('/create', [App\Http\Controllers\Admin\ClientController::class, 'store'])->middleware(['permission:CREATE_CLIENTS'])->name('admin.clients.store');
        Route::get('/{user}/login', [App\Http\Controllers\Admin\ClientController::class, 'loginasClient'])->middleware(['permission:EDIT_CLIENTS'])->name('admin.clients.loginasclient');
        Route::get('/{user}/edit', [App\Http\Controllers\Admin\ClientController::class, 'edit'])->middleware(['permission:VIEW_CLIENTS'])->name('admin.clients.edit');
        Route::post('/{user}/edit', [App\Http\Controllers\Admin\ClientController::class, 'update'])->middleware(['permission:EDIT_CLIENTS'])->name('admin.clients.update');
        Route::delete('/{user}/delete', [App\Http\Controllers\Admin\ClientController::class, 'destroy'])->middleware(['permission:DELETE_CLIENTS'])->name('admin.clients.delete');
        Route::get('/{user}/{orderProduct?}', [App\Http\Controllers\Admin\ClientController::class, 'products'])->middleware(['permission:VIEW_CLIENTS'])->name('admin.clients.products');
        Route::post('/{user}/{orderProduct}/update', [App\Http\Controllers\Admin\ClientController::class, 'updateProduct'])->middleware(['permission:EDIT_CLIENTS'])->name('admin.clients.products.update');
        Route::post('/{user}/{orderProduct}/remove-cancellation', [App\Http\Controllers\Admin\ClientController::class, 'removeCancellation'])->middleware(['permission:EDIT_CLIENTS'])->name('admin.clients.products.removecancellation');
        Route::post('/{user}/{orderProduct}/changeProductStatus', [App\Http\Controllers\Admin\ClientController::class, 'changeProductStatus'])->middleware(['permission:EDIT_CLIENTS'])->name('admin.clients.products.changestatus');
        Route::post('/{user}/{orderProduct}/newProductConfig', [App\Http\Controllers\Admin\ClientController::class, 'newProductConfig'])->middleware(['permission:EDIT_CLIENTS'])->name('admin.clients.products.config.create');
        Route::post('/{user}/{orderProduct}/{orderProductConfig}/update', [App\Http\Controllers\Admin\ClientController::class, 'updateProductConfig'])->middleware(['permission:EDIT_CLIENTS'])->name('admin.clients.products.config.update');
        Route::delete('/{user}/{orderProduct}/{orderProductConfig}/delete', [App\Http\Controllers\Admin\ClientController::class, 'deleteProductConfig'])->middleware(['permission:EDIT_CLIENTS'])->name('admin.clients.products.config.delete');
    });

    Route::group(['prefix' => 'orders'], function () {
        Route::delete('/{order}/{product}/delete', [App\Http\Controllers\Admin\OrderController::class, 'destroyProduct'])->middleware(['permission:EDIT_ORDERS'])->name('admin.orders.deleteProduct');
        Route::get('/', [App\Http\Controllers\Admin\OrderController::class, 'index'])->middleware(['permission:VIEW_ORDERS'])->name('admin.orders');
        Route::delete('/{order}/delete', [App\Http\Controllers\Admin\OrderController::class, 'destroy'])->middleware(['permission:DELETE_ORDERS'])->name('admin.orders.delete');
        Route::get('/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->middleware(['permission:VIEW_ORDERS'])->name('admin.orders.show');
        Route::post('/{order}/{product}/change', [App\Http\Controllers\Admin\OrderController::class, 'changeProduct'])->middleware(['permission:EDIT_ORDERS'])->name('admin.orders.changeProduct');
        Route::post('/{order}/unsuspend', [App\Http\Controllers\Admin\OrderController::class, 'unsuspend'])->middleware(['permission:EDIT_ORDERS'])->name('admin.orders.unsuspend');
        Route::post('/{order}/suspend', [App\Http\Controllers\Admin\OrderController::class, 'suspend'])->middleware(['permission:EDIT_ORDERS'])->name('admin.orders.suspend');
        Route::post('/{order}/create', [App\Http\Controllers\Admin\OrderController::class, 'create'])->middleware(['permission:EDIT_ORDERS'])->name('admin.orders.create');
        Route::post('/{order}/paid', [App\Http\Controllers\Admin\OrderController::class, 'paid'])->middleware(['permission:EDIT_ORDERS'])->name('admin.orders.paid');
    });

    Route::group(['prefix' => 'invoices'], function () {
        Route::get('/', [App\Http\Controllers\Admin\InvoiceController::class, 'index'])->middleware(['permission:VIEW_INVOICES'])->name('admin.invoices');
        Route::get('/create', [App\Http\Controllers\Admin\InvoiceController::class, 'create'])->middleware(['permission:CREATE_INVOICES'])->name('admin.invoices.create');
        Route::post('/create', [App\Http\Controllers\Admin\InvoiceController::class, 'store'])->middleware(['permission:CREATE_INVOICES'])->name('admin.invoices.store');
        Route::get('/{invoice}', [App\Http\Controllers\Admin\InvoiceController::class, 'show'])->middleware(['permission:VIEW_INVOICES'])->name('admin.invoices.show');
        Route::post('/{invoice}/paid', [App\Http\Controllers\Admin\InvoiceController::class, 'paid'])->middleware(['permission:EDIT_INVOICES'])->name('admin.invoices.paid');
    });

    Route::group(['prefix' => 'coupon'], function() {
        Route::get('/', [App\Http\Controllers\Admin\CouponController::class, 'index'])->middleware(['permission:VIEW_COUPONS'])->name('admin.coupons');
        Route::get('/create', [App\Http\Controllers\Admin\CouponController::class, 'create'])->middleware(['permission:EDIT_INVOICES'])->name('admin.coupons.create');
        Route::post('/create', [App\Http\Controllers\Admin\CouponController::class, 'store'])->middleware(['permission:EDIT_INVOICES'])->name('admin.coupons.store');
        Route::get('/{coupon}/edit', [App\Http\Controllers\Admin\CouponController::class, 'edit'])->middleware(['permission:VIEW_INVOICES'])->name('admin.coupons.edit');
        Route::put('/{coupon}/edit', [App\Http\Controllers\Admin\CouponController::class, 'update'])->middleware(['permission:EDIT_INVOICES'])->name('admin.coupons.update');
        Route::delete('/{coupon}/delete', [App\Http\Controllers\Admin\CouponController::class, 'destroy'])->middleware(['permission:EDIT_INVOICES'])->name('admin.coupons.delete');
    });

    Route::group(['prefix' => 'announcements'], function() {
        Route::get('/', [App\Http\Controllers\Admin\AnnouncementController::class, 'index'])->middleware(['permission:VIEW_ANNOUNCEMENTS'])->name('admin.announcements');
        Route::get('/create', [App\Http\Controllers\Admin\AnnouncementController::class, 'create'])->middleware(['permission:CREATE_ANNOUNCEMENTS'])->name('admin.announcements.create');
        Route::post('/create', [App\Http\Controllers\Admin\AnnouncementController::class, 'store'])->middleware(['permission:CREATE_ANNOUNCEMENTS'])->name('admin.announcements.store');
        Route::get('/{announcement}/edit', [App\Http\Controllers\Admin\AnnouncementController::class, 'edit'])->middleware(['permission:EDIT_ANNOUNCEMENTS'])->name('admin.announcements.edit');
        Route::post('/{announcement}/edit', [App\Http\Controllers\Admin\AnnouncementController::class, 'update'])->middleware(['permission:EDIT_INVOICES'])->name('admin.announcements.update');
        Route::delete('/{announcement}/delete', [App\Http\Controllers\Admin\AnnouncementController::class, 'destroy'])->middleware(['permission:DELETE_ANNOUNCEMENTS'])->name('admin.announcements.delete');
    });

    Route::group(['prefix' => 'roles'], function() {
        Route::get('/', [App\Http\Controllers\Admin\RoleController::class, 'index'])->middleware(['permission:VIEW_ROLES'])->name('admin.roles');
        Route::get('/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->middleware(['permission:CREATE_ROLES'])->name('admin.roles.create');
        Route::post('/create', [App\Http\Controllers\Admin\RoleController::class, 'store'])->middleware(['permission:CREATE_ROLES'])->name('admin.roles.store');
        Route::get('/{role}/edit', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->middleware(['permission:VIEW_ROLES'])->name('admin.roles.edit');
        Route::post('/{role}/edit', [App\Http\Controllers\Admin\RoleController::class, 'update'])->middleware(['permission:EDIT_ROLES'])->name('admin.roles.update');
        Route::delete('/{role}/delete', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->middleware(['permission:DELETE_ROLES'])->name('admin.roles.delete');
    });

    Route::group(['prefix' => 'configurable-options'], function() {
        Route::get('/', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'index'])->middleware(['permission:VIEW_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options');
        Route::get('/create', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'create'])->middleware(['permission:CREATE_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.create');
        Route::post('/create', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'store'])->middleware(['permission:CREATE_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.store');
        Route::get('/{configurableOptionGroup}/edit', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'edit'])->middleware(['permission:VIEW_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.edit');
        Route::post('/{configurableOptionGroup}/edit', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'update'])->middleware(['permission:EDIT_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.update');
        Route::delete('/{configurableOptionGroup}/delete', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'destroy'])->middleware(['permission:DELETE_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.delete');

        Route::post('/{configurableOptionGroup}/options/create', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'createOption'])->middleware(['permission:CREATE_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.options.create');
        Route::post('/{configurableOptionGroup}/options/{configurableOption}/edit', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'updateOption'])->middleware(['permission:EDIT_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.options.update');
        Route::post('/{configurableOptionGroup}/options/{configurableOption}/create', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'createOptionInput'])->middleware(['permission:CREATE_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.options.inputs.create');
        Route::delete('/{configurableOptionGroup}/options/{configurableOption}/delete', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'destroyOption'])->middleware(['permission:DELETE_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.options.destroy');
        Route::delete('/{configurableOptionGroup}/options/{configurableOption}/inputs/{configurableOptionInput}/delete', [App\Http\Controllers\Admin\ConfigurableOptionController::class, 'destroyOptionInput'])->middleware(['permission:DELETE_CONFIGURABLE_OPTIONS'])->name('admin.configurable-options.options.inputs.destroy');
    });

    Route::group(['prefix' => 'email'], function() {
        Route::get('/', [App\Http\Controllers\Admin\EmailController::class, 'index'])->middleware(['permission:VIEW_EMAIL'])->name('admin.email');
        Route::get('/templates', [App\Http\Controllers\Admin\EmailController::class, 'templates'])->middleware(['permission:EDIT_EMAIL'])->name('admin.email.templates');
        Route::get('/templates/{template}', [App\Http\Controllers\Admin\EmailController::class, 'template'])->middleware(['permission:EDIT_EMAIL'])->name('admin.email.template');
        Route::post('/templates/{template}/update', [App\Http\Controllers\Admin\EmailController::class, 'update'])->middleware(['permission:EDIT_EMAIL'])->name('admin.email.template.update');
    });

    Route::group(['prefix' => 'taxes'], function() {
        Route::get('/', [App\Http\Controllers\Admin\TaxController::class, 'index'])->middleware(['permission:VIEW_TAXES'])->name('admin.taxes');
        Route::post('/', [App\Http\Controllers\Admin\TaxController::class, 'update'])->middleware(['permission:EDIT_TAXES'])->name('admin.taxes.update');
        Route::post('/create', [App\Http\Controllers\Admin\TaxController::class, 'store'])->middleware(['permission:CREATE_TAXES'])->name('admin.taxes.create');
    }); 

    Route::group(['prefix' => 'logs'], function() {
        Route::get('/', [App\Http\Controllers\Admin\LogController::class, 'index'])->middleware(['permission:VIEW_LOGS'])->name('admin.logs');
        Route::post('/debug', [App\Http\Controllers\Admin\LogController::class, 'debug'])->middleware(['permission:VIEW_LOGS'])->name('admin.logs.debug');
    });
});
