<?php

namespace App\Jobs\Server;

use App\Helpers\ExtensionHelper;
use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CreateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public OrderProduct $orderProduct)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // $data is the data that will be used to send the email, data is coming from the extension itself
        $data = ExtensionHelper::createServer($this->orderProduct);

        // Send the email (TO BE MADE)
        Log::info('Server created', $data);
    }
}
