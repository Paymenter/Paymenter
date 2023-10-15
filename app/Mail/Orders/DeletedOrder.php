<?php

namespace App\Mail\Orders;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use App\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

class DeletedOrder extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;

    /**
     * The products instance.
     * 
     * @var \App\Models\Products
     */
    public $products;

    /**
     * Create a new message instance.
     *
     * @param \App\Models\Order $invoice
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->products = $order->products()->get();
    }
}
