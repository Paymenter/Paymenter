<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Password\Request;
use App\Livewire\Auth\Password\Reset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully()
    {
        Livewire::test(Request::class)
            ->assertHasNoErrors()
            ->assertSee('Email');
    }

    public function test_can_request_password_reset_link()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        Livewire::test(Request::class)
            ->set('email', 'test@example.com')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_cant_request_password_reset_link_with_invalid_email()
    {
        // This is default validation, not disclosing whether the email exists
        Livewire::test(Request::class)
            ->set('email', 'invalid-email')
            ->call('submit')
            ->assertHasErrors(['email' => 'email']);
    }

    public function test_cant_request_password_reset_link_too_many_requests()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        for ($i = 0; $i < 3; $i++) {
            Livewire::test(Request::class)
                ->set('email', 'test@example.com')
                ->call('submit')
                ->assertHasNoErrors()
                ->assertDispatched('notify');
        }
        Livewire::test(Request::class)
            ->set('email', 'test@example.com')
            ->call('submit')
            ->assertHasErrors('email')
            ->assertSee(__('passwords.throttled'));
    }

    public function test_can_request_password_reset_link_with_non_existing_email()
    {
        // This is default validation, not disclosing whether the email exists
        Livewire::test(Request::class)
            ->set('email', 'nonexistent@example.com')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseCount('password_reset_tokens', 0);
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'nonexistent@example.com',
        ]);
    }

    public function test_cant_request_password_reset_link_for_admin_user()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'role_id' => 1,
        ]);
        Livewire::test(Request::class)
            ->set('email', 'admin@example.com')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertDispatched('notify');

        $this->assertDatabaseCount('password_reset_tokens', 0);
        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'admin@example.com',
        ]);
    }

    public function test_can_reset_password_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $token = Password::createToken($user);

        Livewire::withQueryParams(['email' => 'test@example.com'])
            ->test(Reset::class, ['token' => $token])
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_invalid_reset_token_redirects_to_request_form()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        Password::createToken($user);

        $this->get(route('password.reset', ['token' => 'invalid-token', 'email' => 'test@example.com']))
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('notification', ['message' => __('passwords.token'), 'type' => 'error']);
    }

    public function test_reset_link_for_unknown_email_redirects_to_request_form()
    {
        $this->get(route('password.reset', ['token' => 'invalid-token', 'email' => 'nonexistent@example.com']))
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('notification', ['message' => __('passwords.token'), 'type' => 'error']);
    }

    public function test_failed_reset_shows_translated_message()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $token = Password::createToken($user);

        $component = Livewire::withQueryParams(['email' => 'test@example.com'])
            ->test(Reset::class, ['token' => $token]);

        // The link was used up, e.g. in another tab, after the form was opened
        Password::deleteToken($user);

        $component
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('submit')
            ->assertDispatched('notify', ['message' => __('passwords.token'), 'type' => 'error'])
            ->assertNoRedirect();
    }
}
