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
    Route::get('/', [TicketController::class, 'getTickets'])->name('api.client.v1.tickets.getTickets')->middleware('api.permission:ticket:read');
    Route::post('/', [TicketController::class, 'createTicket'])->name('api.client.v1.tickets.createTicket')->middleware('api.permission:ticket:create');
    Route::get('/{ticketId}', [TicketController::class, 'getTicket'])->name('api.client.v1.tickets.getTicket')->middleware('api.permission:ticket:read');
    Route::get('/{ticketId}/messages', [TicketController::class, 'getMessages'])->name('api.client.v1.tickets.getMessages')->middleware('api.permission:ticket:read');
    Route::delete('/{ticketId}/close', [TicketController::class, 'closeTicket'])->name('api.client.v1.tickets.closeTicket')->middleware('api.permission:ticket:update');
    Route::post('/{ticketId}/reply', [TicketController::class, 'replyTicket'])->name('api.client.v1.tickets.replyTicket')->middleware('api.permission:ticket:update');
});

// Invoices
Route::group(['prefix' => 'v1/invoices'], function () {
    Route::get('/', [InvoiceController::class, 'getInvoices'])->name('api.client.v1.invoices.getInvoices')->middleware('api.permission:invoice:read');
    Route::get('/{invoiceId}', [InvoiceController::class, 'getInvoice'])->name('api.client.v1.invoices.getInvoice')->middleware('api.permission:invoice:read');
    Route::post('/{invoiceId}/pay', [InvoiceController::class, 'payInvoice'])->name('api.client.v1.invoices.payInvoice')->middleware('api.permission:invoice:update');
});

