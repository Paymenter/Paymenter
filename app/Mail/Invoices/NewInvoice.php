<?php

namespace App\Mail\Invoices;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use App\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewInvoice extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Invoices
     */
    public $invoice;

    /**
     * The products instance.
     * 
     * @var \App\Models\Products
     */
    public $products;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->products = $invoice->items()->get();
    }
}
