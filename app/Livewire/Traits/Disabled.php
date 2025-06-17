<?php

namespace App\Livewire\Traits;

trait Disabled
{
    public function bootDisabled()
    {
        // Read class-level attributes
        $reflection = new \ReflectionClass($this);
        $attributes = $reflection->getAttributes(\App\Attributes\DisabledIf::class);

        // Check if the DisabledIf attribute is present
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            // Check the condition based on the attribute's setting
            if (config('settings.' . $instance->setting, $instance->default)) {
                // If the condition is met, abort with a 404 error
                abort(404, 'This feature is currently disabled.');
            }
        }
    }
}
