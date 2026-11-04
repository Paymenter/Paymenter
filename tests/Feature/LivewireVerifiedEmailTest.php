<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivewireVerifiedEmailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        config([
            'settings.mail_must_verify' => true,
            'settings.default_currency' => 'USD',
            'settings.ticket_departments' => ['Support'],
        ]);
    }

    /**
     * Load the ticket form and build an update request that creates a ticket from its snapshot.
     */
    private function createTicketPayload(User $user): array
    {
        $html = $this->actingAs($user)->withSession($this->loginUser($user))
            ->get(route('tickets.create'))
            ->assertOk()
            ->getContent();

        preg_match_all('/wire:snapshot="([^"]*)"/', $html, $matches);
        $snapshot = collect($matches[1])
            ->map(fn ($attribute) => htmlspecialchars_decode($attribute, ENT_QUOTES))
            ->first(fn ($snapshot) => json_decode($snapshot, true)['memo']['name'] === 'tickets.create');

        return ['components' => [[
            'snapshot' => $snapshot,
            'updates' => ['subject' => 'Test', 'message' => 'Test message', 'priority' => 'low', 'department' => 'Support'],
            'calls' => [['method' => 'create', 'params' => [], 'metadata' => []]],
        ]]];
    }

    public function test_verified_user_can_use_a_verified_only_component()
    {
        $user = User::factory()->create();
        $payload = $this->createTicketPayload($user);

        $this->postJson(route('paymenter.livewire.update'), $payload, ['X-Livewire' => 'true'])->assertOk();

        $this->assertDatabaseCount('tickets', 1);
    }

    public function test_snapshot_from_before_losing_verification_cannot_be_used()
    {
        $user = User::factory()->create();
        $payload = $this->createTicketPayload($user);

        // e.g. the user changed their email address
        $user->forceFill(['email_verified_at' => null])->save();

        $this->postJson(route('paymenter.livewire.update'), $payload, ['X-Livewire' => 'true'])
            ->assertRedirect(route('verification.notice'));

        $this->assertDatabaseCount('tickets', 0);
    }
}
