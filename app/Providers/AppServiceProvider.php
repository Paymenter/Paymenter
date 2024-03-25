<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;
use Qirolab\Theme\Theme;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            // Load settings from database
            config(['settings' => Setting::where('settingable_type', null)->get()->pluck('value', 'key')]);

            // Set active theme
            Theme::set(config('settings.theme', 'default'));
        } catch (\Exception $e) {
            // Catch exception if database is not available (e.g. during migration)
        }
    }
}
