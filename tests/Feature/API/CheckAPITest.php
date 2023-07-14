<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckAPITest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * Test if API is accessible.
     *
     * @return void
     */
    public function test_if_api_is_accessible()
    {
        // Check if we can access the API without authentication
        $response = $this->get(route('api.client.v1.api.getMe'), [
            'Accept' => 'application/json',
        ]);
        $response->assertStatus(401);
    }

    /**
     * Test if API is accessible with authentication.
     *
     * @return void
     */
    public function test_if_api_is_accessible_with_authentication()
    {
        // Check if we can access the API with authentication
        $response = $this->actingAs($this->user)->get(route('api.client.v1.api.getMe'));
        $response->assertStatus(200);
    }
}
