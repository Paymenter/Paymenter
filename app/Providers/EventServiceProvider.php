<?php

namespace App\Providers;

use App\Models\Extension;
use App\Models\Invoice;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            \SocialiteProviders\Discord\DiscordExtendSocialite::class . '@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Invoice::observe(\App\Observers\InvoiceObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return true;
    }

    /**
     * Get the listener directories that should be used to discover events.
     *
     * @return array<int, string>
     */
    public function discoverEventsWithin(): array
    {
        $paths = [
            $this->app->path('Listeners'),
        ];
        if (!\Illuminate\Support\Facades\Schema::hasTable('extensions')) {
            return $paths;
        }
        Extension::where('type', 'events')->where('enabled', true)->get()->each(function ($extension) {
            $paths[] = $extension->path . '/Listeners';
        });
        return $paths;
    }
}
