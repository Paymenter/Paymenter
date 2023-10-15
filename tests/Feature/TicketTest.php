<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{User, Ticket};

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $categorie;
    protected $product;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test if user can create a ticket.
     *
     * @return void
     */
    public function testCanCreateTicket()
    {
        $response = $this->actingAs($this->user)->post(route('clients.tickets.store'), [
            'title' => 'Test',
            'description' => 'Test Message',
            'priority' => 'low',
        ]);

        $response->assertStatus(302);

        $ticket = Ticket::where('user_id', $this->user->id)->first();

        $this->assertNotNull($ticket);
    }

    /**
     * Test if user can view all the tickets.
     *
     * @return void
     */
    public function testCanViewAllTickets()
    {
        $response = $this->actingAs($this->user)->get(route('clients.tickets.index'));

        $response->assertStatus(200);
    }

    /**
     * Test if user can view a ticket.
     *
     * @return void
     */
    public function testCanViewTicket()
    {
        $ticket = Ticket::factory()->create([
            'title' => 'TEST',
            'status' => 'open',
            'priority' => 'low',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('clients.tickets.show', $ticket));

        $response->assertStatus(200);
    }

    /**
     * Test if user can reply to a ticket.
     * 
     * @return void
     */
    public function testCanReplyToTicket()
    {
        $ticket = Ticket::factory()->create([
            'title' => 'TEST',
            'status' => 'open',
            'priority' => 'low',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('clients.tickets.reply', $ticket), [
            'message' => 'Test',
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('ticket_messages', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->user->id,
            'message' => 'Test',
        ]);
    }

    /**
     * Test if user can close a ticket.
     * 
     * @return void
     */
    public function testCanCloseTicket()
    {
        $ticket = Ticket::factory()->create([
            'title' => 'TEST',
            'status' => 'open',
            'priority' => 'low',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('clients.tickets.close', $ticket));

        $response->assertStatus(302);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => 'closed',
        ]);
    }
}



