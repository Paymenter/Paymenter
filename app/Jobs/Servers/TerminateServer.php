<?php

namespace App\Jobs\Servers;

use App\Helpers\ExtensionHelper;
use App\Models\OrderProduct;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class TerminateServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public OrderProduct $orderProduct
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ExtensionHelper::terminateServer($this->orderProduct);
    }
}
