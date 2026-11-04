<?php

namespace Tests\Feature;

use App\Events\Service\Created as ServiceCreated;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Paymenter\Extensions\Others\DiscordNotifications\DiscordNotifications;
use RuntimeException;
use Tests\TestCase;

class DiscordNotificationsDeliveryTest extends TestCase
{
    use RefreshDatabase;

    private function bootExtension(array $events): void
    {
        (new DiscordNotifications([
            'webhook_url' => 'https://discord.test/api/webhooks/1/token',
            'ping_type' => 'none',
            'ping_id' => null,
            'events' => $events,
        ]))->boot();
    }

    public function test_notification_is_sent_after_the_transaction_commits(): void
    {
        $user = User::factory()->create();

        Http::fake();
        $this->bootExtension(['User Updated']);

        DB::transaction(function () use ($user) {
            $user->update(['first_name' => 'Renamed']);

            Http::assertNothingSent();
        });

        Http::assertSentCount(1);
    }

    public function test_nothing_is_sent_when_the_transaction_rolls_back(): void
    {
        $user = User::factory()->create();

        Http::fake();
        $this->bootExtension(['User Updated']);

        try {
            DB::transaction(function () use ($user) {
                $user->update(['first_name' => 'Renamed']);

                throw new RuntimeException('Rolled back');
            });
        } catch (RuntimeException) {
        }

        Http::assertNothingSent();
    }

    public function test_a_failed_request_does_not_propagate_in_debug_mode(): void
    {
        config(['settings.debug' => true]);
        $user = User::factory()->create();

        Http::fake(fn () => throw new ConnectionException('Connection timed out'));
        $this->bootExtension(['User Updated']);

        DB::transaction(fn () => $user->update(['first_name' => 'Renamed']));

        $this->assertSame('Renamed', $user->fresh()->first_name);
    }

    public function test_an_error_while_building_the_message_does_not_propagate_in_debug_mode(): void
    {
        config(['settings.debug' => true]);
        Http::fake();
        $this->bootExtension(['Service Created']);

        // No user or product to read from
        event(new ServiceCreated(new Service));

        Http::assertNothingSent();
    }
}
