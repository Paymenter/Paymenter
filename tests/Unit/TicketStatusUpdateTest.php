<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_ticket_status_gets_updated_on_new_message(): void
    {
        $user = \App\Models\User::factory()->create();
        $ticket = \App\Models\Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        \App\Models\TicketMessage::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $user->id]);

        $this->assertEquals('open', $ticket->fresh()->status);

        // Simulate a new message created by a different user
        $anotherUser = \App\Models\User::factory()->create();
        \App\Models\TicketMessage::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $anotherUser->id]);

        // Assert that the ticket status is updated to 'replied'
        $this->assertEquals('replied', $ticket->fresh()->status);
    }
}
