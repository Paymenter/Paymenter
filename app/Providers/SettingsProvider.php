<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;
use Qirolab\Theme\Theme;


class SettingsProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->getSettings();
    }

    public static function getSettings()
    {
        try {
            // Load settings from cache
            $settings = Cache::get('settings', []);
            if (empty($settings)) {
                $settings = Setting::where('settingable_type', null)->get()->pluck('value', 'key');
                Cache::put('settings', $settings);
            }
            config(['settings' => $settings]);
        
            Theme::set(config('settings.theme', 'default'), 'default');
        } catch (\Exception $e) {
            // Do nothing
        }
    }

    public static function flushCache()
    {
        Cache::forget('settings');
        self::getSettings();
    }
}
