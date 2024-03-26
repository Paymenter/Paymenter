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
        // Load settings from cache
        $settings = Cache::get('settings', Setting::where('settingable_type', null)->get()->pluck('value', 'key'));
        config(['settings' => $settings]);

        Theme::set(config('settings.theme', 'default'), 'default');
    }
}
