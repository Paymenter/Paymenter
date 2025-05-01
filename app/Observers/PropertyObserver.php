<?php

namespace App\Observers;

use App\Events\Property as PropertyEvent;
use App\Models\Property;

class PropertyObserver
{
    /**
     * Handle the Property "creating" event.
     */
    public function created(Property $property): void
    {
        event(new PropertyEvent\Created($property));
    }

    /**
     * Handle the Property "updating" event.
     */
    public function updated(Property $property): void
    {
        event(new PropertyEvent\Updated($property));
    }

    /**
     * Handle the Property "deleted" event.
     */
    public function deleted(Property $property): void
    {
        event(new PropertyEvent\Deleted($property));
    }
}
