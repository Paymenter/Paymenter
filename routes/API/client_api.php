<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Clients\TicketController;
use App\Http\Controllers\API\Clients\APIController;
use App\Http\Controllers\API\Clients\UserController;
use App\Http\Controllers\API\Clients\InvoiceController;

// API
Route::group(['prefix' => 'v1/'], function () {
    Route::get('/permissions', [APIController::class, 'getPermissions'])->name('api.client.v1.api.getPermissions');
    Route::get('/me', [APIController::class, 'getMe'])->name('api.client.v1.api.getMe');
});

// Tickets
Route::group(['prefix' => 'v1/tickets'], function () {
    Route::get('/', [TicketController::class, 'getTickets'])->name('api.client.v1.tickets.getTickets');
    Route::post('/', [TicketController::class, 'createTicket'])->name('api.client.v1.tickets.createTicket');
    Route::get('/{ticketId}', [TicketController::class, 'getTicket'])->name('api.client.v1.tickets.getTicket');
    Route::get('/{ticketId}/messages', [TicketController::class, 'getMessages'])->name('api.client.v1.tickets.getMessages');
    Route::delete('/{ticketId}/close', [TicketController::class, 'closeTicket'])->name('api.client.v1.tickets.closeTicket');
    Route::post('/{ticketId}/reply', [TicketController::class, 'replyTicket'])->name('api.client.v1.tickets.replyTicket');
});

// Invoices
Route::group(['prefix' => 'v1/invoices'], function () {
    Route::get('/', [InvoiceController::class, 'getInvoices'])->name('api.client.v1.invoices.getInvoices');
    Route::get('/{invoiceId}', [InvoiceController::class, 'getInvoice'])->name('api.client.v1.invoices.getInvoice');
    Route::post('/{invoiceId}/pay', [InvoiceController::class, 'payInvoice'])->name('api.client.v1.invoices.payInvoice');
});
