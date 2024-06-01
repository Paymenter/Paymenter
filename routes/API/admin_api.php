<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Admin\APIController;
use App\Http\Controllers\API\Admin\InvoiceController;
use App\Http\Controllers\API\Admin\TicketController;
use App\Http\Controllers\API\Admin\UserController;

// Users
Route::group(['prefix' => 'v1/users'], function () {
    Route::get('/', [UserController::class, 'getUser'])->name('api.admin.v1.users.getUser');
});

// Tickets
Route::group(['prefix' => 'v1/tickets'], function () {
    Route::get('/', [TicketController::class, 'getTickets'])->name('api.admin.v1.tickets.getTickets')->middleware('api.permission:admin:ticket:read');
    Route::post('/', [TicketController::class, 'createTicket'])->name('api.admin.v1.tickets.createTickets')->middleware('api.permission:admin:ticket:create');
    Route::get('/{ticketId}', [TicketController::class, 'getTicket'])->name('api.admin.v1.tickets.getTicket')->middleware('api.permission:admin:ticket:read');
    Route::get('/{ticketId}/messages', [TicketController::class, 'getTicketMessages'])->name('api.admin.v1.tickets.getTicketMessages')->middleware('api.permission:admin:ticket:read');
    Route::put('/{ticketId}/status', [TicketController::class, 'updateTicketStatus'])->name('api.admin.v1.tickets.updateTicketStatus')->middleware('api.permission:admin:ticket:update');
    Route::post('/{ticketId}/reply', [TicketController::class, 'replyTicket'])->name('api.admin.v1.tickets.replyTicket')->middleware('api.permission:admin:ticket:update');
});

// Invoices
Route::group(['prefix' => 'v1/invoices'], function () {
    Route::get('/', [InvoiceController::class, 'getInvoices'])->name('api.admin.v1.invoices.getInvoices')->middleware('api.permission:admin:invoice:read');
    Route::get('/{invoiceId}', [InvoiceController::class, 'getInvoice'])->name('api.admin.v1.invoices.getInvoice')->middleware('api.permission:admin:invoice:read');
});

// Credits
Route::group(['prefix' => 'v1/credits'], function () {
    Route::get('/{userId}', [UserController::class, 'getCredits'])->name('api.admin.v1.credits.getCredits')->middleware('api.permission:admin:credits:read');
    Route::put('/{userId}', [UserController::class, 'setCredits'])->name('api.admin.v1.credits.setCredit')->middleware('api.permission:admin:credits:update');
});

// API
Route::group(['prefix' => 'v1'], function () {
    Route::get('/permissions', [APIController::class, 'getPermissions'])->name('api.admin.v1.api.getPermissions');
    Route::post('/token', [APIController::class, 'createAPIToken'])->name('api.admin.v1.api.createAPIToken');
});
