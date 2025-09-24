<?php

namespace App\Events\Setting;

use App\Models\Setting;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class Saved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Setting $setting)
    {
        // This event is dispatched after a setting is saved.
        // We are gonna overwrite the value of the setting
        if ($setting->settingable_type === null) {
            $cSetting = \App\Classes\Settings::getSetting($setting->key);
            // Set the config value for the setting
            $settings = config('settings', []);
            $settings[$setting->key] = $setting->value;
            Config::set('settings', $settings);
            // Does it have overrides?
            if (isset($cSetting->override) && config("settings.$cSetting->name") !== null) {
                Config::set($cSetting->override, config("settings.$cSetting->name"));
            }
        }
    }
}
