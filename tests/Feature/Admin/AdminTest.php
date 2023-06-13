<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminTest extends TestCase
{
    use RefreshDatabase;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role_id' => 1]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testNonAdminRedirect()
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);
    }

    /**
     * Login as admin.
     *
     * @return void
     */
    public function testCanLoginAsAdmin()
    {
        $user = User::factory()->create(['role_id' => 1]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    /**
     * Tests admin page.
     *
     * @return void
     */
    public function testAuthenticatedAsAdminCanAccessAdmin()
    {
        $response = $this->actingAs($this->user)->get('/admin');

        $response->assertStatus(200);
    }

    /**
     * Tests admin page.
     *
     * @return void
     */
    public function testAuthenticatedAsAdminCanAccessAdminUsers()
    {
        $response = $this->actingAs($this->user)->get('/admin/clients');

        $response->assertStatus(200);
    }

    /**
     * Tests admin page.
     *
     * @return void
     */
    public function testAuthenticatedAsAdminCanAccessAdminSettings()
    {
        $response = $this->actingAs($this->user)->get('/admin/settings');

        $response->assertStatus(302);

        $response->assertRedirect(route('password.confirm'));

        $response->assertSessionHas('url.intended', route('admin.settings'));

        $response = $this->actingAs($this->user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.settings'));
    }

    /**
     * Tests admin page.
     *
     * @return void
     */
    public function testAuthenticatedAsAdminCanAccessAdminOrders()
    {
        $response = $this->actingAs($this->user)->get('/admin/orders');

        $response->assertStatus(200);
    }

    /**
     * Tests admin page.
     *
     * @return void
     */
    public function testAuthenticatedAsAdminCanAccessAdminProducts()
    {
        $response = $this->actingAs($this->user)->get('/admin/products');

        $response->assertStatus(200);
    }
}
