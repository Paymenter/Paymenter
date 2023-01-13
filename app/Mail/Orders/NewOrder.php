<?php

namespace App\Mail\Orders;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Orders;
use Illuminate\Mail\Mailables\Content;

class NewOrder extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Orders
     */
    protected $order;


    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Orders  $invoice
     * @return void
     */
    public function __construct(Orders $order)
    {
        $this->order = $order;
    }


    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.orders.new',
            with: [
                'order' => $this->order,
                'products' => $this->order->products()->get(),
            ]
        );
    }
}
