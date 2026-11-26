<?php

namespace App\Http\Requests\Auth;

use Laravel\Passkeys\Http\Requests\PasskeyVerificationRequest as BasePasskeyVerificationRequest;

class PasskeyVerificationRequest extends BasePasskeyVerificationRequest
{
    public function remember(): bool
    {
        // Paymenter's login response handles the original remember input using user sessions.
        return false;
    }
}
