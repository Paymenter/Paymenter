<?php

namespace App\Jobs\Server;

use App\Helpers\ExtensionHelper;
use App\Helpers\NotificationHelper;
use App\Models\Service;
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
    public function __construct(public Service $service, public $sendNotiication = true) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $data = ExtensionHelper::suspendServer($this->service);
        } catch (\Exception $e) {
            if ($e->getMessage() == 'No server assigned to this product') {
                return;
            }
        }

        // Send the email (TO BE MADE)
        if ($this->sendNotiication) {
            NotificationHelper::serverTerminatedNotification($this->service->order->user, $this->service, $data);
        }
    }
}