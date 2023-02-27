<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Models\Tickets;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => 1]);
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

        $ticket = Tickets::factory()->create([
            'title' => 'TEST',
            'description' => 'TEST',
            'status' => 'open',
            'priority' => 'low',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.tickets.edit', $ticket));
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->get(route('admin.tickets'));
        $response->assertStatus(200);
    }

}
