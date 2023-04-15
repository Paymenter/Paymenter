<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Clients\TicketController;
use App\Http\Controllers\API\Clients\APIController;
use App\Http\Controllers\API\Clients\UserController;
use App\Http\Controllers\API\Clients\InvoiceController;

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
    Route::get('/{invoiceId}/pay', [InvoiceController::class, 'payInvoice'])->name('api.client.v1.invoices.payInvoice');
});

// API
Route::group(['prefix' => 'v1/api'], function () {
    Route::get('/permissions', [APIController::class, 'getPermissions'])->name('api.client.v1.api.getPermissions');
});