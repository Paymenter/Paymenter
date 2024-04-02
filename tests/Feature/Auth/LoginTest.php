<?php
 
namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Livewire\CreatePost;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;
 
class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** 
     * @test
     * Test if the login page renders successfully
     */
    public function renders_successfully()
    {
        Livewire::test(Login::class)
            ->assertHasNoErrors()
            ->assertSee('Email')
            ->assertSee('Password');
    }

    /** 
     * @test 
     * Test if the user can't login with invalid credentials
     */
    public function cant_login_with_invalid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@paymenter')
            ->set('password', 'password')
            ->call('submit')
            ->assertHasErrors('email');
    }

    /** 
     * @test
     * Test if the user can login with valid credentials
     */
    public function can_login_with_valid_credentials()
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
     * @test 
     * Test if the user can't login with valid credentials but wrong captcha
     */
    public function cant_login_with_captcha_enabled()
    {
        Setting::where('key', 'captcha')->get()->first()->update(['value' => 'turnstile']);
        
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
     * @test
     * Test if the user can login with valid credentials and redirect to intended url
     */
    public function can_login_with_valid_credentials_and_redirect_to_intended_url()
    {        
        User::factory()->create([
            'email' => 'test@paymenter.org',
            'password' => Hash::make('password'),
        ]);

        $this->get('/admin/settings')->assertRedirect(route('login'));

        Livewire::test(Login::class)
            ->set('email', 'test@paymenter.org')
            ->set('password', 'password')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.settings'));

        $this->assertAuthenticated();

        $this->get('/admin/settings')->assertOk();

    }

    /** 
     * @test 
     * Test if user gets redirected to the login page if they are not authenticated
     */
    public function cant_go_to_admin_dashboard_if_not_authenticated()
    {
        $this->get('/admin/settings')->assertRedirect(route('login'));
    }

    /** 
     * @test
     * Test if the user is authenticated but not an admin and tries to access the admin dashboard
     */
    public function cant_go_to_admin_dashboard_if_authenticated_but_not_admin()
    {
        $user = User::factory()->create();

      //  $this->actingAs($user)->get('/admin/settings')->assertForbidden();
    }
}