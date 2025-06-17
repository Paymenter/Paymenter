<?php

// Attribute for disabling a component based on a condition
namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DisabledIf
{
    function __construct(
        public string $setting,
        public bool $default = false,
    ) { }
}
