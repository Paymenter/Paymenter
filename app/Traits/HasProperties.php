<?php

namespace App\Traits;

use App\Models\Property;

trait HasProperties
{
    /**
     * Get the properties of the model which has this trait
     */
    public function properties()
    {
        return $this->morphMany(Property::class, 'model');
    }
}
