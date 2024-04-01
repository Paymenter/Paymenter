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

    /** @test */
    public function renders_successfully()
    {
        Livewire::test(Login::class)
            ->assertHasNoErrors()
            ->assertSee('Email')
            ->assertSee('Password');
    }

    /** @test */
    public function cant_login_with_invalid_credentials()
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@paymenter')
            ->set('password', 'password')
            ->call('submit')
            ->assertHasErrors('email');
    }

    /** @test */
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


    /** @test */
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

        Livewire::test(Login::class)
            ->set('email', 'test@paymenter.org')
            ->set('password', 'password')
            ->call('submit')
            ->assertHasErrors('captcha');

        $this->assertGuest();

    }

    /** @test */
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
}