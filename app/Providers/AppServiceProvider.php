<?php

namespace App\Providers;

use App\Models\Affiliate;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Route;
use Qirolab\Theme\Theme;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

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
        Validator::extend('domain', 'App\\Validators\\Domain@validate');
        Validator::replacer('domain', 'App\\Validators\\Domain@message');
        Schema::defaultStringLength(191);
        if (Str::startsWith(config('app.url') ?? '', 'https://')) {
            URL::forceScheme('https');
        }
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/paymenter/live/update', $handle)->middleware('web');
        });

        // Check if request contains ?ref= parameter
        if (request()->has('ref')) {
            // Check if affiliate code exists
            $affiliate = Affiliate::where('code', request()->ref)->first();
            if ($affiliate) {
                $affiliate->increment('visitors');
                if (!auth()->check()) {
                    // Set affiliate cookie
                    cookie()->queue('affiliate', $affiliate->code, 60 * 24 * 90);
                }
            }
        }
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
            if (empty(config('settings::mail_host')) || empty(config('settings::mail_port')) || empty(config('settings::mail_username')) || empty(config('settings::mail_password'))) {
                config(['settings::mail_disabled' => true]);
            }

            if (!config('settings::mail_disabled')) {
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
                }
            }
            if (config('settings::timezone') !== config('app.timezone')) {
                config(['app.timezone' => config('settings::timezone')]);
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
            // Unset settings::theme
            config(['settings::theme-active' => config('settings::theme')]);
            config(['settings::theme' => null]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
        // @markdownify (markdown and purify html)
        Blade::directive('markdownify', function ($value): string {
            return "<?php
                \$environment = new League\CommonMark\Environment\Environment([]);
                \$environment->addExtension(new League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
                \$environment->addExtension(new League\CommonMark\Extension\GithubFlavoredMarkdownExtension());
                \$converter = new League\CommonMark\MarkdownConverter(\$environment);
                \$value2 = \Stevebauman\Purify\Facades\Purify::clean($value);
                echo preg_replace('/(<br \/>)+$/', '', nl2br(\$converter->convertToHtml(\$value2)));
            ?>";
        });
    }
}
