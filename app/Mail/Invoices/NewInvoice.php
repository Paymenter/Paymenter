<?php

namespace App\Mail\Invoices;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoices;
use Illuminate\Mail\Mailables\Content;

class NewInvoice extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Invoices
     */
    protected $invoice;


    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Invoices  $invoice
     * @return void
     */
    public function __construct(Invoices $invoice)
    {
        $this->invoice = $invoice;
    }

 
    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.invoices.new',
            with: [
                'invoice' => $this->invoice,
            ]
        );
    }

}
