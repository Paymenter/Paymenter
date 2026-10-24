<?php

namespace Tests\Unit;

use App\Support\TicketMailId;
use Tests\TestCase;

class TicketMailIdTest extends TestCase
{
    public function test_parses_its_own_message_id(): void
    {
        $localPart = TicketMailId::localPart(42);

        $this->assertMatchesRegularExpression('/^42\.[a-f0-9]{24}$/', $localPart);
        $this->assertSame(42, TicketMailId::parse($localPart . '@example.com'));
        $this->assertSame(42, TicketMailId::parse('<' . $localPart . '@example.com>'));
    }

    public function test_rejects_ids_without_a_valid_token(): void
    {
        $token = substr(TicketMailId::localPart(42), 3);

        $this->assertNull(TicketMailId::parse('42@example.com'));
        $this->assertNull(TicketMailId::parse('43.' . $token . '@example.com'));
        $this->assertNull(TicketMailId::parse('42.' . str_repeat('0', 24) . '@example.com'));
        $this->assertNull(TicketMailId::parse('42.' . strtoupper($token) . '@example.com'));
        $this->assertNull(TicketMailId::parse('something-else@example.com'));
    }

    public function test_token_depends_on_the_app_key(): void
    {
        $localPart = TicketMailId::localPart(42);

        config(['app.key' => 'base64:' . base64_encode(random_bytes(32))]);

        $this->assertNull(TicketMailId::parse($localPart . '@example.com'));
    }
}
