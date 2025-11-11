<?php

namespace App\Observers;

use App\Events\Notification as NotificationEvents;
use App\Models\Notification;

class NotificationObserver
{
    /**
     * Handle the Notification "creating" event.
     */
    public function creating(Notification $notification): void
    {
        event(new NotificationEvents\Creating($notification));
    }

    /**
     * Handle the Notification "created" event.
     */
    public function created(Notification $notification): void
    {
        event(new NotificationEvents\Created($notification));
    }

    /**
     * Handle the Notification "updating" event.
     */
    public function updating(Notification $notification): void
    {
        event(new NotificationEvents\Updating($notification));
    }

    /**
     * Handle the Notification "updated" event.
     */
    public function updated(Notification $notification): void
    {
        event(new NotificationEvents\Updated($notification));
    }

    /**
     * Handle the Notification "deleting" event.
     */
    public function deleting(Notification $notification): void
    {
        event(new NotificationEvents\Deleting($notification));
    }

    /**
     * Handle the Notification "deleted" event.
     */
    public function deleted(Notification $notification): void
    {
        event(new NotificationEvents\Deleted($notification));
    }
}
