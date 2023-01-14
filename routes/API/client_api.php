<?php

use App\Http\Controllers\API\Clients\InvoiceController;
use App\Http\Controllers\API\Clients\TicketController;
use App\Http\Controllers\API\Clients\UserController;
use Illuminate\Support\Facades\Route;

// Users
Route::group(['prefix' => 'v1/users', 'middleware' => 'auth'], function () {
    Route::get('/', [UserController::class, 'getUser'])->name('api.client.v1.users.getUser');
});

// Tickets
Route::group(['prefix' => 'v1/tickets', 'middleware' => 'auth'], function () {
    Route::get('/', [TicketController::class, 'getTickets'])->name('api.client.v1.tickets.getTickets');
    Route::post('/', [TicketController::class, 'createTickets'])->name('api.client.v1.tickets.createTickets');
    Route::get('/{ticketId}', [TicketController::class, 'getTicket'])->name('api.client.v1.tickets.getTicket');
    Route::delete('/{ticketId}/close', [TicketController::class, 'closeTicket'])->name('api.client.v1.tickets.closeTicket');
    Route::put('/{ticketId}/reply', [TicketController::class, 'replyTicket'])->name('api.client.v1.tickets.replyTicket');
});

// Invoices
Route::group(['prefix' => 'v1/invoices', 'middleware' => 'auth'], function () {
    Route::get('/', [InvoiceController::class, 'getInvoices'])->name('api.client.v1.invoices.getInvoices');
    Route::get('/{invoiceId}', [InvoiceController::class, 'getInvoice'])->name('api.client.v1.invoices.getInvoice');
    Route::get('/{invoiceId}/pay', [InvoiceController::class, 'payInvoice'])->name('api.client.v1.invoices.payInvoice');
});