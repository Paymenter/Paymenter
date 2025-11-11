<?php

namespace App\Listeners;

use App\Events\Setting\Saved;
use App\Providers\SettingsProvider;

class FlushCacheListener
{
    public function handle(Saved $event)
    {
        if ($event->setting->settingable_type !== null) {
            return;
        }
        static $flushed = false;
        if (!$flushed) {
            app()->terminating(fn () => SettingsProvider::flushCache());
            $flushed = true;
        }
    }
}
