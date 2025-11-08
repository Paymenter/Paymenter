<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Hash;

/**
 * Helper class for verifying Blesta-style passwords
 * 
 * Blesta uses a two-step password hashing:
 * 1. systemHash(password) - HMAC SHA-256 with system_key
 * 2. bcrypt(systemHash(password)) - Standard bcrypt on the HMAC result
 */
class BlestaPasswordHelper
{
    /**
     * Verify a password against a Blesta-style hash
     * 
     * @param string $password The plain text password
     * @param string $hash The stored password hash
     * @param string|null $systemKey The Blesta system_key (null to use from settings)
     * @return bool True if password matches, false otherwise
     */
    public static function verify(string $password, string $hash, ?string $systemKey = null): bool
    {
        // Get system key from parameter or settings
        if ($systemKey === null) {
            $systemKey = \App\Models\Setting::where('key', 'blesta_system_key')->value('value');
            
            if (!$systemKey) {
                // No system key configured, can't verify Blesta passwords
                return false;
            }
        }

        // Blesta's systemHash creates an HMAC SHA-256 hash
        $systemHash = self::systemHash($password, $systemKey);
        
        // Then verify using password_verify (bcrypt)
        return password_verify($systemHash, $hash);
    }

    /**
     * Create a Blesta-style system hash (HMAC SHA-256)
     * 
     * @param string $value The value to hash
     * @param string $systemKey The Blesta system_key
     * @return string The hex-encoded HMAC hash
     */
    public static function systemHash(string $value, string $systemKey): string
    {
        return bin2hex(hash_hmac('sha256', $value, $systemKey, true));
    }

    /**
     * Check if a password hash appears to be a Blesta-style hash
     * This is a heuristic check - Blesta hashes are bcrypt hashes that start with $2y$
     * 
     * @param string $hash The password hash to check
     * @return bool True if it looks like a Blesta hash (bcrypt format)
     */
    public static function isBlestaHash(string $hash): bool
    {
        // Blesta uses bcrypt, which starts with $2y$ or $2a$ or $2b$
        return preg_match('/^\$2[ayb]\$/', $hash) === 1;
    }

    /**
     * Verify password with fallback to standard Laravel hash
     * Tries Blesta verification first, then falls back to standard Hash::check
     * 
     * @param string $password The plain text password
     * @param string $hash The stored password hash
     * @param string|null $systemKey Optional Blesta system_key
     * @return bool True if password matches
     */
    public static function verifyWithFallback(string $password, string $hash, ?string $systemKey = null): bool
    {
        // First try Blesta verification if system key exists
        $blestaKey = $systemKey ?? \App\Models\Setting::where('key', 'blesta_system_key')->value('value');
        
        if ($blestaKey && self::isBlestaHash($hash)) {
            $blestaVerified = self::verify($password, $hash, $blestaKey);
            if ($blestaVerified) {
                return true;
            }
        }

        // Fall back to standard Laravel hash verification
        return Hash::check($password, $hash);
    }
}

