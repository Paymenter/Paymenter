<?php

// Define an attribute for extension metadata

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ExtensionMeta
{
    public function __construct(
        public string $name,
        public string $description,
        public string $version,
        public string $author,
        public string $url = '',
        public string $icon = '',
    ) {}
}
