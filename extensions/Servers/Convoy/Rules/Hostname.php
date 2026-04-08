<?php

namespace Paymenter\Extensions\Servers\Convoy\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Hostname implements ValidationRule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  mixed  $value
     */
    public function validate(string $attribute, $value, Closure $fail): void
    {
        if (!(bool) filter_var($value, FILTER_VALIDATE_DOMAIN)) {
            $fail(__('validation.hostname'));
        }
    }
}
