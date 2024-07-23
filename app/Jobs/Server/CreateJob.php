<?php

namespace App\Jobs\Server;

use App\Helpers\ExtensionHelper;
use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        ExtensionHelper::createServer($this->orderProduct);
    }
}
