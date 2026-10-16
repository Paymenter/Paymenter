<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Paymenter\Extensions\Others\DiscordNotifications\DiscordNotifications;
use Tests\TestCase;

class DiscordNotificationsTest extends TestCase
{
    use RefreshDatabase;

    private const WEBHOOK = 'https://discord.test/api/webhooks/1/token';

    private function bootExtension(array $events): void
    {
        (new DiscordNotifications([
            'webhook_url' => self::WEBHOOK,
            'ping_type' => 'none',
            'ping_id' => null,
            'events' => $events,
        ]))->boot();
    }

    private function sentFields(): array
    {
        $fields = [];
        Http::assertSent(function (Request $request) use (&$fields) {
            $fields = $request->data()['embeds'][0]['fields'];

            return $request->url() === self::WEBHOOK;
        });

        return array_column($fields, 'value', 'name');
    }

    public function test_user_updated_masks_hidden_and_encrypted_attributes(): void
    {
        $user = User::factory()->create(['tfa_secret' => 'JBSWY3DPEHPK3PXP']);

        Http::fake();
        $this->bootExtension(['User Updated']);

        $user->update(['tfa_secret' => null, 'first_name' => 'Renamed']);

        $fields = $this->sentFields();

        $this->assertSame('********', $fields['Tfa secret']);
        $this->assertStringContainsString('-> Renamed', $fields['First name']);
        Http::assertSent(fn (Request $request) => !str_contains($request->body(), 'JBSWY3DPEHPK3PXP'));
    }

    public function test_password_change_is_masked(): void
    {
        $user = User::factory()->create();

        Http::fake();
        $this->bootExtension(['User Updated']);

        $user->update(['password' => 'a-new-password']);

        $this->assertSame('********', $this->sentFields()['Password']);
    }

    public function test_long_values_are_cut_to_the_field_limit(): void
    {
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id]);

        Http::fake();
        $this->bootExtension(['Ticket Replied']);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => str_repeat('é', 2000),
        ]);

        $value = $this->sentFields()['Message'];
        $this->assertSame(1024, mb_strlen($value));
        $this->assertTrue(mb_check_encoding($value, 'UTF-8'));
    }
}
