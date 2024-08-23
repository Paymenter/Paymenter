<?php

namespace App\Listeners;

use App\Events\Invoice\Paid;
use App\Jobs\Server\CreateJob;
use App\Jobs\Server\UnsuspendJob;

class InvoicePaidListener
{
    /**
     * Handle the event.
     */
    public function handle(Paid $event): void
    {
        // Update order products if invoice is paid (suspended -> active etc.)
        $event->invoice->items->each(function ($item) {
            $orderProduct = $item->orderProduct;
            if (!$orderProduct || $orderProduct->status == 'active' || !$orderProduct->product->server) {
                return;
            }
            $orderProduct->status = 'active';
            if ($orderProduct->status == 'suspended') {
                UnsuspendJob::dispatch($orderProduct);
            } elseif ($orderProduct->status == 'pending') {
                CreateJob::dispatch($orderProduct);
                $orderProduct->status = 'pending-setup';
            }
            $orderProduct->expires_at = $orderProduct->calculateNextDueDate();
            $orderProduct->save();
        });
    }
}
