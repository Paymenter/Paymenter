<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Password\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('Too many password reset attempts. Please try again later.');
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
}
