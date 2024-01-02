<?php

namespace App\Providers;

use App\Models\Affiliate;
use App\Models\Announcement;
use App\Models\Extension;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
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
        TicketMessage::observe(\App\Observers\TicketMessageObserver::class);
        Ticket::observe(\App\Observers\TicketObserver::class);
        User::observe(\App\Observers\UserObserver::class);
        Affiliate::observe(\App\Observers\AffiliateObserver::class);
        Announcement::observe(\App\Observers\AnnouncementObserver::class);
        try {
            foreach (Extension::where('enabled', true)->get() as $extension) {
                $module = $extension->namespace . 'Listeners';
                if (!class_exists($module)) {
                    continue;
                }
                Event::subscribe(new $module);
            }
        } catch (\Exception $e) {
            // If the database is not yet migrated, this will throw an exception.
        }
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
        return [
            $this->app->path('Listeners'),
        ];
    }
}
