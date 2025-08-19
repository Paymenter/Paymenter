<?php

namespace App\Redactors;

use Crypt;

class RightRedactor implements \OwenIt\Auditing\Contracts\AttributeRedactor
{
    /**
     * {@inheritdoc}
     */
    public static function redact($value): string
    {
        try {
            // Try to decrypt value
            $value = Crypt::decryptString($value);
        } catch (\Exception $e) {
        }

        $total = strlen($value);
        $tenth = (int) ceil($total / 10);

        // Make sure single character strings get redacted
        $length = ($total > $tenth) ? ($total - $tenth) : 1;

        return str_pad(substr($value, 0, -$length), $total, '#', STR_PAD_RIGHT);
    }
}
