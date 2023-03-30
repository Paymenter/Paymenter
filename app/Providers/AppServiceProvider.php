<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Contracts\Encryption\DecryptException;
use Qirolab\Theme\Theme;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
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
            $settings = Setting::all();
            foreach ($settings as $setting) {
                config(['settings::' . $setting->key => $setting->value]);
            }
            if (config('settings::app_name') !== config('app.name')) {
                config(['app.name' => config('settings::app_name')]);
            }
            if (config('settings::mail_host') !== config('mail.host')) {
                config(['mail.host' => config('settings::mail_host')]);
            }
            if (config('settings::mail_port') !== config('mail.port')) {
                config(['mail.port' => config('settings::mail_port')]);
            }
            if (config('settings::mail_username') !== config('mail.username')) {
                config(['mail.username' => config('settings::mail_username')]);
            }
            if (config('settings::mail_password') !== config('mail.password')) {
                try {
                    $passwordDecrypt = Crypt::decryptString(config('settings::mail_password'));
                    config(['mail.password' => $passwordDecrypt]);
                } catch (DecryptException $e) {
                    config(['mail.password' => config('settings::mail_password')]);
                }
            }
            if (config('settings::mail_encryption') !== config('mail.encryption')) {
                config(['mail.encryption' => config('settings::mail_encryption')]);
            }
            if (config('settings::mail_from_address') !== config('mail.from.address')) {
                config(['mail.from.address' => config('settings::mail_from_address')]);
            }
            if (config('settings::mail_from_name') !== config('mail.from.name')) {
                config(['mail.from.name' => config('settings::mail_from_name')]);
            }
            if (
                config('mail.mailers.smtp.host') != config('settings::mail_host') ||
                config('mail.mailers.smtp.port') != config('settings::mail_port') ||
                config('mail.mailers.smtp.username') != config('settings::mail_username') ||
                config('mail.mailers.smtp.password') != config('settings::mail_password') ||
                config('mail.from.address') != config('settings::mail_from_address') ||
                config('mail.from.name') != config('settings::mail_from_name')
            ) {
                config(['mail.mailers.smtp' => [
                    'transport' => 'smtp',
                    'host' => config('settings::mail_host'),
                    'port' => config('settings::mail_port'),
                    'encryption' => config('settings::mail_encryption'),
                    'username' => config('settings::mail_username'),
                    'password' => config('mail.password'),
                    'timeout' => null,
                    'auth_mode' => null,
                ]]);
                config(['mail.from' => ['address' => config('settings::mail_from_address'), 'name' => config('settings::mail_from_name')]]);

                Artisan::call('queue:restart');
            }
            if (config('settings::discord_enabled')) {
                config(['services.discord.client_id' => config('settings::discord_client_id')]);
                config(['services.discord.client_secret' => config('settings::discord_client_secret')]);
                config(['services.discord.redirect' => url('/login/discord/callback')]);
            }
            if (config('settings::google_enabled')) {
                config(['services.google.client_id' => config('settings::google_client_id')]);
                config(['services.google.client_secret' => config('settings::google_client_secret')]);
                config(['services.google.redirect' => url('/login/google/callback')]);
            }
            if (config('settings::github_enabled')) {
                config(['services.github.client_id' => config('settings::github_client_id')]);
                config(['services.github.client_secret' => config('settings::github_client_secret')]);
                config(['services.github.redirect' => url('/login/github/callback')]);
            }
            if (config('settings::currency') == null) {
                config(['settings::currency' => 'USD']);
            }
            if (config('settings::theme') !== config('themes.active')) {
                Theme::set(config('settings::theme'), 'default');
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
