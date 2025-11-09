<?php

namespace App\Auth;

use App\Helpers\BlestaPasswordHelper;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

/**
 * Custom User Provider that supports both Blesta-style passwords and standard Laravel passwords
 * 
 * This provider extends EloquentUserProvider to add Blesta password verification.
 * It checks Blesta passwords first (if system_key is configured), then falls back to standard bcrypt.
 */
class BlestaUserProvider extends EloquentUserProvider implements UserProvider
{
    /**
     * Validate a user against the given credentials.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @param  array  $credentials
     * @return bool
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = $credentials['password'] ?? null;
        
        if (!$plain) {
            return false;
        }

        // Try Blesta password verification first (if system key exists)
        // Then fall back to standard Laravel hash verification
        return BlestaPasswordHelper::verifyWithFallback($plain, $user->getAuthPassword());
    }
}

