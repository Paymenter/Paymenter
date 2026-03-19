<?php

namespace Tests\Unit;

use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;
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
        $user = User::factory()->create();
        $ticket = Ticket::factory()->create(['user_id' => $user->id, 'status' => 'open']);
        TicketMessage::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $user->id]);

        $this->assertEquals('open', $ticket->fresh()->status);

        // Simulate a new message created by a different user
        $anotherUser = User::factory()->create();
        TicketMessage::factory()->create(['ticket_id' => $ticket->id, 'user_id' => $anotherUser->id]);

        // Assert that the ticket status is updated to 'replied'
        $this->assertEquals('replied', $ticket->fresh()->status);
    }
}
