<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Admin\APIController;
use App\Http\Controllers\API\Admin\InvoiceController;
use App\Http\Controllers\API\Admin\TicketController;
use App\Http\Controllers\API\Admin\UserController;

// Users
Route::group(['prefix' => 'v1/users'], function () {
    Route::get('/', [UserController::class, 'getUser'])->name('api.client.v1.users.getUser');
});

// Tickets
Route::group(['prefix' => 'v1/tickets'], function () {
    Route::get('/', [TicketController::class, 'getTickets'])->name('api.client.v1.tickets.getTickets');
    Route::post('/', [TicketController::class, 'createTicket'])->name('api.client.v1.tickets.createTickets');
    Route::get('/{ticketId}', [TicketController::class, 'getTicket'])->name('api.client.v1.tickets.getTicket');
    Route::delete('/{ticketId}/close', [TicketController::class, 'closeTicket'])->name('api.client.v1.tickets.closeTicket');
    Route::put('/{ticketId}/reply', [TicketController::class, 'replyTicket'])->name('api.client.v1.tickets.replyTicket');
});

// Invoices
Route::group(['prefix' => 'v1/invoices'], function () {
    Route::get('/', [InvoiceController::class, 'getInvoices'])->name('api.client.v1.invoices.getInvoices');
    Route::get('/{invoiceId}', [InvoiceController::class, 'getInvoice'])->name('api.client.v1.invoices.getInvoice');
});

// API
Route::group(['prefix' => 'v1/api'], function () {
    Route::get('/permissions', [APIController::class, 'getPermissions'])->name('api.client.v1.api.getPermissions');
    Route::post('/token', [APIController::class, 'createAPIToken'])->name('api.client.v1.api.createAPIToken');
});
