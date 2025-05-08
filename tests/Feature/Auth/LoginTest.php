<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the login page renders successfully
     */
    public function test_renders_successfully()
    {
        Livewire::test(Login::class)
            ->assertHasNoErrors()
            ->assertSee('Email')
            ->assertSee('Password');
    }

    /**
     * Test if the user can't login with invalid credentials
     */
    public function test_cant_login_with_invalid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@paymenter')
            ->set('password', 'password')
            ->call('submit')
            ->assertHasErrors('email');
    }

    /**
     * Test if the user can login with valid credentials
     */
    public function test_can_login_with_valid_credentials()
    {
        User::factory()->create([
            'email' => 'tests@paymenter.org',
            'password' => Hash::make('password'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'tests@paymenter.org')
            ->set('password', 'password')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    /**
     * Test if the user can't login with valid credentials but wrong captcha
     */
    public function test_cant_login_with_captcha_enabled()
    {
        // Use captcha 6Lfi6dkkAAAAAMoS7Ya74nsiIQv840dQpPdDIYqT and 6Lfi6dkkAAAAAEdMgMWZB0VqTh6lpeBHYoxff78n
        config([
            'settings.captcha' => 'recaptcha-v3',
            'settings.captcha_site_key' => '6Lfi6dkkAAAAAMoS7Ya74nsiIQv840dQpPdDIYqT',
            'settings.captcha_secret' => '6Lfi6dkkAAAAAEdMgMWZB0VqTh6lpeBHYoxff78n',
        ]);

        Livewire::test(Login::class)
            ->set('email', 'test@paymenter.org')
            ->set('password', 'password')
            ->set('captcha', 'wrong')
            ->call('submit')
            ->assertHasErrors('captcha');

        $this->assertGuest();

        // Test without captcha
        Livewire::test(Login::class)
            ->set('email', 'test@paymenter.org')
            ->set('password', 'password')
            ->call('submit')
            ->assertHasErrors('captcha');

        $this->assertGuest();
    }

    /**
     * Test if the user can login with valid credentials and redirect to intended url
     */
    public function test_can_login_with_valid_credentials_and_redirect_to_intended_url()
    {
        User::factory()->create([
            'email' => 'test@paymenter.org',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);

        $this->get('/admin/settings')->assertRedirect(route('login'));

        Livewire::test(Login::class)
            ->set('email', 'test@paymenter.org')
            ->set('password', 'password')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect('/admin/settings');

        $this->assertAuthenticated();

        $this->get('/admin/settings')->assertOk();
    }

    /**
     * Test if user gets redirected to the login page if they are not authenticated
     */
    public function test_cant_go_to_admin_dashboard_if_not_authenticated()
    {
        $this->get('/admin/settings')->assertRedirect(route('login'));
    }

    /**
     * Test if the user is authenticated but not an admin and tries to access the admin dashboard
     */
    public function test_cant_go_to_admin_dashboard_if_authenticated_but_not_admin()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/admin/settings')->assertForbidden();
    }
}
