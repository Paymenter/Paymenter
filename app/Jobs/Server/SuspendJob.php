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

class SuspendJob implements ShouldQueue
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
            $data = ExtensionHelper::suspendServer($this->service);
        } catch (Exception $e) {
            if ($e->getMessage() !== 'No server assigned to this product') {
                throw $e;
            }
        }

        if ($this->sendNotification) {
            // Find the pending renewal invoice for this service so we can
            // include the amount due and a direct payment link in the notification
            $pendingInvoice = $this->service->invoices()
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($pendingInvoice) {
                $data['invoice'] = $pendingInvoice;
                $data['invoiceTotal'] = $pendingInvoice->formattedTotal;
                $data['invoiceItems'] = $pendingInvoice->items;
            }

            NotificationHelper::serverSuspendedNotification($this->service->user, $this->service, is_array($data) ? $data : []);
        }
    }
}
