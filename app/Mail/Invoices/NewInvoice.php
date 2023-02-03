<?php

namespace App\Mail\Invoices;

use App\Models\Invoices;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
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
    protected $invoice;

    /**
     * Create a new message instance.
     *
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
