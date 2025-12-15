<?php

namespace App\Jobs\Server;

use App\Helpers\ExtensionHelper;
use App\Helpers\NotificationHelper;
use App\Models\Service;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TerminateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;

    public $tries = 1;

    /**
     * Create a new job instance.
     */
    public function __construct(public Service $service, public $sendNotification = true) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = [];

        try {
            $data = ExtensionHelper::terminateServer($this->service);
        } catch (Exception $e) {
            if ($e->getMessage() !== 'No server assigned to this product') {
                throw $e;
            }
        }

        if ($this->sendNotification) {
            NotificationHelper::serverTerminatedNotification($this->service->user, $this->service, is_array($data) ? $data : []);
        }
    }
}
