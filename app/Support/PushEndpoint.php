<?php

namespace App\Support;

final class PushEndpoint
{
    // https://raw.githubusercontent.com/pushpad/known-push-services/refs/heads/master/whitelist
    private const WHITELIST = [
        'android.googleapis.com',
        'fcm.googleapis.com',
        'jmt17.google.com',
        'updates.push.services.mozilla.com',
        'updates-autopush.stage.mozaws.net',
        'updates-autopush.dev.mozaws.net',
        '*.notify.windows.com',
        '*.push.apple.com',
    ];

    public static function isAllowed(string $endpoint): bool
    {
        $parts = parse_url($endpoint);

        if (
            !is_array($parts)
            || ($parts['scheme'] ?? null) !== 'https'
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['port'])
        ) {
            return false;
        }

        $host = strtolower(rtrim($parts['host'], '.'));

        foreach (self::WHITELIST as $allowedHost) {
            if ($allowedHost === $host) {
                return true;
            }

            if (str_starts_with($allowedHost, '*.')) {
                $suffix = substr($allowedHost, 1);
                if (str_ends_with($host, $suffix) && $host !== substr($suffix, 1)) {
                    return true;
                }
            }
        }

        return false;
    }

    // RFC 8291: p256dh is an uncompressed P-256 public key (65 bytes), auth a 16-byte secret, both base64url
    public static function hasValidKeys(mixed $keys): bool
    {
        return is_array($keys)
            && self::decodedLength($keys['p256dh'] ?? null) === 65
            && self::decodedLength($keys['auth'] ?? null) === 16;
    }

    private static function decodedLength(mixed $value): ?int
    {
        if (!is_string($value) || !preg_match('/^[A-Za-z0-9_-]+={0,2}$/', $value)) {
            return null;
        }

        $decoded = base64_decode(strtr(rtrim($value, '='), '-_', '+/'), true);

        return $decoded === false ? null : strlen($decoded);
    }
}
