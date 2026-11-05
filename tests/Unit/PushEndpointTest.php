<?php

namespace Tests\Unit;

use App\Support\PushEndpoint;
use PHPUnit\Framework\TestCase;

class PushEndpointTest extends TestCase
{
    public function test_allowed_push_endpoints(): void
    {
        foreach (self::allowedEndpoints() as [$endpoint]) {
            $this->assertTrue(PushEndpoint::isAllowed($endpoint));
        }
    }

    public function test_rejected_push_endpoints(): void
    {
        foreach (self::rejectedEndpoints() as [$endpoint]) {
            $this->assertFalse(PushEndpoint::isAllowed($endpoint));
        }
    }

    public static function allowedEndpoints(): array
    {
        return [
            ['https://fcm.googleapis.com/fcm/send/abc'],
            ['https://updates.push.services.mozilla.com/wpush/v2/abc'],
            ['https://foo.notify.windows.com/wpush/v2/abc'],
            ['https://foo.push.apple.com/3/device/abc'],
        ];
    }

    public static function rejectedEndpoints(): array
    {
        return [
            ['http://fcm.googleapis.com/fcm/send/abc'],
            ['https://example.com/push'],
            ['https://fcm.googleapis.com:443/fcm/send/abc'],
            ['https://push.apple.com/3/device/abc'],
            ['https://foo.push.apple.com:8443/3/device/abc'],
            ['https://user:password@fcm.googleapis.com/fcm/send/abc'],
            ['https://user@fcm.googleapis.com/fcm/send/abc'],
        ];
    }
}
