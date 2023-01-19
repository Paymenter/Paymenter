<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Providers\RouteServiceProvider;

class AdminTest extends TestCase
{
    
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['is_admin' => 1]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_non_admin_redirect()
    {
        $response = $this->get('/admin');

        $response->assertStatus(302);
    }


    /**
     * Login as admin
     * 
     * @return void
     */
    public function test_can_login_as_admin()
    {
        $user = User::factory()->create(['is_admin' => 1]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
    /**
     * Tests admin page
     * 
     * @return void
     */
    public function test_authentiacted_as_admin_can_access_admin()
    {
        $response = $this->actingAs($this->user)->get('/admin');

        $response->assertStatus(200);
    }

    /**
     * Tests admin page
     * 
     * @return void
     */
    public function test_authentiacted_as_admin_can_access_admin_users()
    {
        $response = $this->actingAs($this->user)->get('/admin/clients');

        $response->assertStatus(200);
    }

    /**
     * Tests admin page
     * 
     * @return void
     */
    public function test_authentiacted_as_admin_can_access_admin_settings()
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
     * Tests admin page
     * 
     * @return void
     */
    public function test_authentiacted_as_admin_can_access_admin_orders()
    {
        $response = $this->actingAs($this->user)->get('/admin/orders');

        $response->assertStatus(200);
    }

    /**
     * Tests admin page
     * 
     * @return void
     */
    public function test_authentiacted_as_admin_can_access_admin_products()
    {
        $response = $this->actingAs($this->user)->get('/admin/products');

        $response->assertStatus(200);
    }

}
