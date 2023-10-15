<?php

namespace Tests\Feature\API\Client;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TicketTest extends TestCase
{
    protected $user;
    protected $ticket;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->ticket = Ticket::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test if user can get all the tickets.
     */
    public function test_if_user_can_get_all_the_tickets()
    {
        $response = $this->actingAs($this->user)->getJson(route('api.client.v1.tickets.getTickets'));
        $response->assertStatus(200);
    }

    /**
     * Test if user can create a ticket.
     */
    public function test_if_user_can_create_a_ticket()
    {
        $response = $this->actingAs($this->user)->postJson(route('api.client.v1.tickets.createTicket'), [
            'title' => 'TEST',
            'message' => 'TEST',
            'priority' => 'low',
        ]);
        $response->assertStatus(201);
    }

    /**
     * Test if user can't create a ticket without a body.
     */
    public function test_if_user_cant_create_a_ticket_without_a_body()
    {
        $response = $this->actingAs($this->user)->postJson(route('api.client.v1.tickets.createTicket'), [
            
        ]);
        $response->assertStatus(422);
    }

    /**
     * Test if user can get a ticket.
     */
    public function test_if_user_can_get_a_ticket()
    {
        $response = $this->actingAs($this->user)->getJson(route('api.client.v1.tickets.getTicket', $this->ticket));
        $response->assertStatus(200);
    }

    /**
     * Test if user can get ticket messages.
     */
    public function test_if_user_can_get_ticket_messages()
    {
        $response = $this->actingAs($this->user)->getJson(route('api.client.v1.tickets.getMessages', $this->ticket));
        $response->assertStatus(200);
    }

    /**
     * Test if user can reply to a ticket.
     */
    public function test_if_user_can_reply_to_a_ticket()
    {
        $response = $this->actingAs($this->user)->postJson(route('api.client.v1.tickets.replyTicket', $this->ticket), [
            'message' => 'TEST',
        ]);
        $response->assertStatus(201);
    }
    
    /**
     * Test if user can close a ticket.
     */
    public function test_if_user_can_close_a_ticket()
    {
        $response = $this->actingAs($this->user)->deleteJson(route('api.client.v1.tickets.closeTicket', $this->ticket));
        $response->assertStatus(200);
    }
    
}
