<?php

namespace App\Redactors;

use OwenIt\Auditing\Contracts\AttributeRedactor;

class FullRedactor implements AttributeRedactor
{
    /**
     * Replace the whole value, so no part of a secret ends up in the audit log.
     * Empty values stay empty, so the log still shows a secret being set or cleared.
     */
    public static function redact($value): string
    {
        return $value === null || $value === '' ? '' : '[redacted]';
    }
}
