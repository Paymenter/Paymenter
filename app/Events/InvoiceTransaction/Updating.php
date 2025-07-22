<?php

namespace App\Events\InvoiceTransaction;

use App\Models\InvoiceTransaction;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Updating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public InvoiceTransaction $invoiceTransaction) {}
}
