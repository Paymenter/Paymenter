<?php

namespace App\Providers;

use App\Models\Extension;
use App\Classes\Synths\PriceSynth;
use App\Helpers\ExtensionHelper;
use App\Models\EmailLog;
use App\Models\OauthClient;
use App\Models\User;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use League\CommonMark\Extension\Table\TableExtension;

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

        foreach (Extension::where('enabled', true)->get() as $extension) {
            ExtensionHelper::call($extension, 'boot', mayFail: true);
        }

        Queue::after(function (JobProcessed $event) {
            if ($event->job->resolveName() === 'App\Mail\Mail') {
                $payload = json_decode($event->job->getRawBody());
                $data = unserialize($payload->data->command);
                EmailLog::where('id', $data->mailable->email_log_id)->update([
                    'sent_at' => now(),
                    'status' => 'sent',
                ]);
            }
        });
        Queue::failing(function (JobFailed $event) {
            if ($event->job->resolveName() === 'App\Mail\Mail') {
                $payload = json_decode($event->job->getRawBody());
                $data = unserialize($payload->data->command);
                EmailLog::where('id', $data->mailable->email_log_id)->update([
                    'status' => 'failed',
                    'error' => $event->exception->getMessage(),
                    'job_uuid' => $event->job->uuid(),
                ]);
            }
        });

        Str::macro('markdown', function ($markdown) {
            return Str::markdown($markdown, extensions: [
                new TableExtension,
            ]);
        });
        Passport::clientModel(OauthClient::class);
        Passport::ignoreRoutes();
        Passport::tokensCan([
            'profile' => 'View your profile',
        ]);
    }
}
