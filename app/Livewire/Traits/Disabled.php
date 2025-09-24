<?php

namespace App\Livewire\Traits;

use App\Attributes\DisabledIf;
use ReflectionClass;

trait Disabled
{
    public function bootDisabled()
    {
        // Read class-level attributes
        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes(DisabledIf::class);

        // Check if the DisabledIf attribute is present
        foreach ($attributes as $attribute) {
            $instance = $attribute->newInstance();
            // Check the condition based on the attribute's setting
            $condition = config('settings.' . $instance->setting, $instance->default);
            if ($instance->reverse) {
                $condition = !$condition;
            }
            // If the condition is met, abort with a 404 error
            if ($condition) {
                // If the condition is met, abort with a 404 error
                abort(404, 'This feature is currently disabled.');
            }
        }
    }
}
