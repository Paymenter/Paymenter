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
}
