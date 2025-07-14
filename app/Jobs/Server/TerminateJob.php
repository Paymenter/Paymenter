<?php

namespace App\Jobs\Server;

use Exception;
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
    public function __construct(public Service $service, public $sendNotification = true) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $data = ExtensionHelper::terminateServer($this->service);
        } catch (Exception $e) {
            if ($e->getMessage() == 'No server assigned to this product') {
                return;
            }
        }

        // Send the email (TO BE MADE)
        if ($this->sendNotification && isset($data)) {
            NotificationHelper::serverTerminatedNotification($this->service->order->user, $this->service, is_array($data) ? $data : []);
        }
    }
}
