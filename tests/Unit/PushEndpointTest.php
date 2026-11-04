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

    public function test_valid_push_keys(): void
    {
        $this->assertTrue(PushEndpoint::hasValidKeys(self::keys()));
        $this->assertTrue(PushEndpoint::hasValidKeys(['p256dh' => self::base64Url(65) . '=', 'auth' => self::base64Url(16) . '==']));
    }

    public function test_invalid_push_keys(): void
    {
        $this->assertFalse(PushEndpoint::hasValidKeys(null));
        $this->assertFalse(PushEndpoint::hasValidKeys([]));
        $this->assertFalse(PushEndpoint::hasValidKeys(['p256dh' => self::keys()['p256dh']]));
        $this->assertFalse(PushEndpoint::hasValidKeys(['auth' => self::keys()['auth']]));
        $this->assertFalse(PushEndpoint::hasValidKeys(['p256dh' => ['array'], 'auth' => self::keys()['auth']]));
        $this->assertFalse(PushEndpoint::hasValidKeys(['p256dh' => self::base64Url(64), 'auth' => self::keys()['auth']]));
        $this->assertFalse(PushEndpoint::hasValidKeys(['p256dh' => self::keys()['p256dh'], 'auth' => self::base64Url(32)]));
        $this->assertFalse(PushEndpoint::hasValidKeys(['p256dh' => self::keys()['p256dh'], 'auth' => 'not base64url!!!!!!!!!']));
    }

    private static function keys(): array
    {
        return ['p256dh' => self::base64Url(65), 'auth' => self::base64Url(16)];
    }

    private static function base64Url(int $bytes): string
    {
        return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
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
