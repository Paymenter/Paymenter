<?php

namespace App\Providers;

use App\Classes\Synths\PriceSynth;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Service provider for settings
        $this->app->register(SettingsProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Change livewire url
        \Livewire\Livewire::setUpdateRoute(function ($handle) {
            return \Illuminate\Support\Facades\Route::post('/paymenter/update', $handle)->middleware('web')->name('paymenter.update');
        });
        \Livewire\Livewire::propertySynthesizer(PriceSynth::class);

        Gate::define('has-permission', function (User $user, string $ability) {
            return $user->hasPermission($ability);
        });

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('discord', \SocialiteProviders\Discord\Provider::class);
        });

        // Register views for extensions (app/Extensions/{type}/{extension}/views)
        $this->loadViewsFrom(app_path('Extensions'), 'extensions');
    }
}
