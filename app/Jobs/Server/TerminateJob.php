<?php

namespace App\Jobs\Server;

use App\Helpers\ExtensionHelper;
use App\Helpers\NotificationHelper;
use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TerminateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public OrderProduct $orderProduct, public $sendNotiication = true) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = ExtensionHelper::suspendServer($this->orderProduct);

        // Send the email (TO BE MADE)
        if ($this->sendNotiication) {
            NotificationHelper::serverTerminatedNotification($this->orderProduct->order->user, $this->orderProduct, $data);
        }
    }
}
