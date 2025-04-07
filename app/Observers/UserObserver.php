<?php

namespace App\Observers;

use App\Events\User as UserEvent;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        event(new UserEvent\Creating($user));
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        event(new UserEvent\Created($user));
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        event(new UserEvent\Updated($user));
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }
}
