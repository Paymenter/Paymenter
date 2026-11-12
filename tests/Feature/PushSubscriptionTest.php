<?php

namespace Tests\Feature;

use App\Livewire\Client\Notifications;
use App\Models\NotificationSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PushSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    private function keys(): array
    {
        return [
            'p256dh' => rtrim(strtr(base64_encode(random_bytes(65)), '+/', '-_'), '='),
            'auth' => rtrim(strtr(base64_encode(random_bytes(16)), '+/', '-_'), '='),
        ];
    }

    // Called directly: the page's breadcrumb declares a global function, so it can only render once per process
    private function subscribe(array $subscription): void
    {
        (new Notifications)->storePushSubscription(json_encode($subscription));
    }

    public function test_subscription_is_stored(): void
    {
        $this->subscribe(['endpoint' => 'https://fcm.googleapis.com/fcm/send/abc', 'keys' => $this->keys()]);

        $this->assertSame(1, $this->user->pushSubscriptions()->count());
    }

    public function test_subscription_without_keys_is_refused(): void
    {
        $this->expectException(ValidationException::class);

        try {
            $this->subscribe(['endpoint' => 'https://fcm.googleapis.com/fcm/send/abc']);
        } finally {
            $this->assertSame(0, NotificationSubscription::count());
        }
    }

    public function test_subscription_with_malformed_keys_is_refused(): void
    {
        $this->expectException(ValidationException::class);

        $this->subscribe([
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/abc',
            'keys' => ['p256dh' => $this->keys()['p256dh'], 'auth' => 'short'],
        ]);
    }

    public function test_oldest_subscriptions_are_replaced_beyond_ten(): void
    {
        foreach (range(1, 10) as $i) {
            $this->subscribe(['endpoint' => 'https://fcm.googleapis.com/fcm/send/' . $i, 'keys' => $this->keys()]);
        }

        $this->subscribe(['endpoint' => 'https://fcm.googleapis.com/fcm/send/11', 'keys' => $this->keys()]);

        $endpoints = $this->user->pushSubscriptions()->pluck('endpoint');
        $this->assertCount(10, $endpoints);
        $this->assertNotContains('https://fcm.googleapis.com/fcm/send/1', $endpoints);
        $this->assertContains('https://fcm.googleapis.com/fcm/send/11', $endpoints);
    }

    public function test_resubscribing_an_existing_endpoint_keeps_it(): void
    {
        foreach (range(1, 10) as $i) {
            $this->subscribe(['endpoint' => 'https://fcm.googleapis.com/fcm/send/' . $i, 'keys' => $this->keys()]);
        }

        $this->subscribe(['endpoint' => 'https://fcm.googleapis.com/fcm/send/1', 'keys' => $this->keys()]);

        $endpoints = $this->user->pushSubscriptions()->pluck('endpoint');
        $this->assertCount(10, $endpoints);
        $this->assertContains('https://fcm.googleapis.com/fcm/send/1', $endpoints);
    }
}
