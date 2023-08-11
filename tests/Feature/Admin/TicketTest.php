<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use RefreshDatabase;
    protected $user;
    protected $client;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role_id' => 1]);
        $this->client = User::factory()->create();
    }


    /** 
     * Test if admin can view all the ticket pages
     *
     * @return void
     */
    public function testIfAdminCanViewAllTheTicketPages()
    {
        $response = $this->actingAs($this->user)->get(route('admin.tickets'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->get(route('admin.tickets.create'));
        $response->assertStatus(200);

        $ticket = Ticket::factory()->create([
            'title' => 'TEST',
            'status' => 'open',
            'priority' => 'low',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.tickets.show', $ticket));
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->get(route('admin.tickets'));
        $response->assertStatus(200);
    }

    /**
     * Test if admin can create a ticket
     *
     * @return void
     */
    public function testIfAdminCanCreateATicket()
    {
        $response = $this->actingAs($this->user)->post(route('admin.tickets.store'), [
            'title' => 'TEST',
            'description' => 'Testing Message',
            'status' => 'open',
            'priority' => 'low',
            'user' => $this->client->id,
        ]);

        $response->assertStatus(302);

        $this->assertDatabaseHas('tickets', [
            'title' => 'TEST',
            'status' => 'open',
            'priority' => 'low',
            'user_id' => $this->client->id,
        ]);
    }

    /**
     * Test if admin can update a ticket
     *
     * @return void
     */
    public function testIfAdminCanChangeStatus()
    {
        $ticket = Ticket::factory()->create([
            'title' => 'TEST',
            'status' => 'open',
            'priority' => 'low',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.tickets.update', $ticket), [
            'title' => 'TEST',
            'status' => 'closed',
            'priority' => 'low',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.tickets.show', $ticket));
        
        $this->assertDatabaseHas('tickets', [
            'title' => 'TEST',
            'status' => 'closed',
            'priority' => 'low',
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test if admin can reply to a ticket
     * 
     * @return void
     */
    public function testIfAdminCanReplyToATicket()
    {
        $ticket = Ticket::factory()->create([
            'title' => 'TEST',
            'status' => 'open',
            'priority' => 'low',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.tickets.reply', $ticket), [
            'message' => 'Test Message',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('admin.tickets.show', $ticket));
        
        $this->assertDatabaseHas('ticket_messages', [
            'message' => 'Test Message',
            'ticket_id' => $ticket->id,
            'user_id' => $this->user->id,
        ]);
    }
}
