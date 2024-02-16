<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\ServiceProvider;

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
        } catch (\Exception $e) {
            // Catch exception if database is not available (e.g. during migration)
        }
    }
}
