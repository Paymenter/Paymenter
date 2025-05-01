<?php

namespace App\Observers;

use App\Events\Property as PropertyEvent;
use App\Models\Property;

class PropertyObserver
{
    public function created(Property $property): void
    {
        event(new PropertyEvent\Created($property));
    }

    public function updated(Property $property): void
    {
        event(new PropertyEvent\Updated($property));
    }

    public function deleted(Property $property): void
    {
        event(new PropertyEvent\Deleted($property));
    }
}
