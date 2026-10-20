<?php

namespace App\Support;

final class ClientIp
{
    /**
     * The key a per-IP rate limit should count against.
     *
     * An IPv6 user is usually given a whole /64 and can pick any address in it,
     * so IPv6 addresses are counted per /64. IPv4 (and IPv4-mapped IPv6) is
     * counted per address.
     */
    public static function key(?string $ip = null): string
    {
        $ip ??= (string) request()->ip();
        $packed = @inet_pton($ip);

        if ($packed === false || strlen($packed) !== 16) {
            return $ip;
        }

        if (str_starts_with($packed, str_repeat("\0", 10) . "\xff\xff")) {
            return (string) inet_ntop(substr($packed, 12));
        }

        return inet_ntop(substr($packed, 0, 8) . str_repeat("\0", 8)) . '/64';
    }
}
