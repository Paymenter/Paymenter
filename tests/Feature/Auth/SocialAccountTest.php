<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Register;
use App\Livewire\Client\Security;
use App\Models\CustomProperty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class SocialAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        CustomProperty::each(function ($property) {
            $property->required = false;
            $property->save();
        });
    }

    public function test_social_registration_creates_a_verified_user_and_connected_account(): void
    {
        config(['settings.oauth_discord' => true]);
        session()->put('social_login.registration', [
            'provider' => 'discord',
            'provider_user_id' => '123456789',
            'email' => 'discord@example.test',
        ]);

        Livewire::test(Register::class)
            ->assertSet('socialRegistration', true)
            ->assertSet('email', 'discord@example.test')
            ->set('first_name', 'Discord')
            ->set('last_name', 'User')
            ->set('password', 'passw0rd')
            ->set('password_confirmation', 'passw0rd')
            ->call('submit')
            ->assertHasNoErrors();

        $user = User::where('email', 'discord@example.test')->firstOrFail();

        $this->assertNotNull($user->email_verified_at);
        $this->assertDatabaseHas('social_accounts', [
            'user_id' => $user->id,
            'provider' => 'discord',
            'provider_user_id' => '123456789',
        ]);
        $this->assertFalse(session()->has('social_login.registration'));
    }

    public function test_social_account_can_only_be_disconnected_after_password_confirmation(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('passw0rd'),
        ]);
        $user->socialAccounts()->create([
            'provider' => 'discord',
            'provider_user_id' => '123456789',
            'linked_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(Security::class)
            ->call('startDisconnectSocialAccount', 'discord')
            ->set('disconnectPassword', 'not-the-password')
            ->call('disconnectSocialAccount')
            ->assertHasErrors('disconnectPassword');

        $this->assertDatabaseHas('social_accounts', ['provider_user_id' => '123456789']);

        Livewire::actingAs($user)
            ->test(Security::class)
            ->call('startDisconnectSocialAccount', 'discord')
            ->set('disconnectPassword', 'passw0rd')
            ->call('disconnectSocialAccount')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('social_accounts', ['provider_user_id' => '123456789']);
    }
}
