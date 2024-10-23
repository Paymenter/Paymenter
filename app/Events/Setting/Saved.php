<?php

namespace App\Events\Setting;

use App\Providers\SettingsProvider;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class Saved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct()
    {
        SettingsProvider::flushCache();
    }
}
