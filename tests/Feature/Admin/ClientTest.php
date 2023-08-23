<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role_id' => 1]);
    }

    /**
     * Can admin view all the clients.
     *
     * @return void
     */
    public function testIfAdminCanViewAllTheClients()
    {
        $response = $this->actingAs($this->user)->get(route('admin.clients'));
        $response->assertStatus(200);
    }

    /**
     * Can admin view a client.
     *
     * @return void
     */
    public function testIfAdminCanViewAClient()
    {
        $client = User::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.clients.edit', $client));
        $response->assertStatus(200);
    }

    /**
     * Can admin create a client.
     *
     * @return void
     */
    public function testIfAdminCanCreateAClient()
    {
        $response = $this->actingAs($this->user)->post(route('admin.clients.store'), [
            'first_name' => 'TEST',
            'last_name' => 'test',
            'email' => 'client@paymenter.org',
            'password' => 'password',
        ]);

        $response->assertStatus(302);
    }

    /**
     * Can admin update a client.
     *
     * @return void
     */
    public function testIfAdminCanUpdateAClient()
    {
        $client = User::factory()->create();

        $response = $this->actingAs($this->user)->post(route('admin.clients.update', $client), [
            'first_name' => 'TEST',
        ]);

        $response->assertStatus(302);
    }
}
