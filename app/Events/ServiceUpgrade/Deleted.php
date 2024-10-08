<?php

namespace App\Events\ServiceUpgrade;

use App\Models\ServiceUpgrade;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Deleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public ServiceUpgrade $serviceUpgrade) {}
}
