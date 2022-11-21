<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Models\Settings;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('recaptcha', 'App\\Validators\\ReCaptcha@validate');
        Schema::defaultStringLength(191);
        try {
            $settings = Settings::all();
            foreach ($settings as $setting) {
                config(['settings::' . $setting->key => $setting->value]);
            }
            if (config('settings::app_name') !== config('app.name')) {
                config(['app.name' => config('settings::app_name')]);
            }
        } catch (\Exception $e) {
            // do nothing
        }
    }
}
