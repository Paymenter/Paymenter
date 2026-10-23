<?php

namespace Tests\Feature\Client;

use App\Livewire\Client\Account;
use App\Livewire\Client\Security;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;
use Tests\TestCase;

class AccountSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_name_can_be_changed_without_password()
    {
        $user = User::factory()->create(['first_name' => 'Old']);

        Livewire::actingAs($user)->test(Account::class)
            ->set('first_name', 'New')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertSame('New', $user->fresh()->first_name);
    }

    public function test_email_change_requires_current_password()
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        Livewire::actingAs($user)->test(Account::class)
            ->set('email', 'new@example.com')
            ->call('submit')
            ->assertHasErrors('current_password');

        Livewire::actingAs($user)->test(Account::class)
            ->set('email', 'new@example.com')
            ->set('current_password', 'wrong-password')
            ->call('submit')
            ->assertHasErrors('current_password');

        $this->assertSame('old@example.com', $user->fresh()->email);

        Livewire::actingAs($user)->test(Account::class)
            ->set('email', 'new@example.com')
            ->set('current_password', 'password')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('current_password', '');

        $user->refresh();
        $this->assertSame('new@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    public function test_password_guesses_are_limited()
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        for ($i = 0; $i < 5; $i++) {
            Livewire::actingAs($user)->test(Account::class)
                ->set('email', 'new@example.com')
                ->set('current_password', 'wrong-password')
                ->call('submit')
                ->assertHasErrors('current_password');
        }

        Livewire::actingAs($user)->test(Account::class)
            ->set('email', 'new@example.com')
            ->set('current_password', 'password')
            ->call('submit')
            ->assertHasErrors('current_password');

        $this->assertSame('old@example.com', $user->fresh()->email);

        // The password change form shares the same budget.
        Livewire::actingAs($user)->test(Security::class)
            ->set('current_password', 'password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('changePassword');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_email_changes_are_limited()
    {
        $user = User::factory()->create(['email' => 'old@example.com']);

        for ($i = 1; $i <= 3; $i++) {
            Livewire::actingAs($user)->test(Account::class)
                ->set('email', "new{$i}@example.com")
                ->set('current_password', 'password')
                ->call('submit')
                ->assertHasNoErrors();
        }

        Livewire::actingAs($user)->test(Account::class)
            ->set('email', 'new4@example.com')
            ->set('current_password', 'password')
            ->call('submit')
            ->assertHasErrors('email');

        $this->assertSame('new3@example.com', $user->fresh()->email);
    }

    public function test_disabling_two_factor_requires_a_valid_code()
    {
        $tfa = new TwoFactorAuth(new EndroidQrCodeProvider, config('app.name'));
        $secret = $tfa->createSecret();
        $user = User::factory()->create(['tfa_secret' => $secret]);

        Livewire::actingAs($user)->test(Security::class)
            ->call('disableTwoFactor')
            ->assertHasErrors('disableTwoFactorCode');

        $wrongCode = str_pad((string) (((int) $tfa->getCode($secret)) + 500000) % 1000000, 6, '0', STR_PAD_LEFT);

        Livewire::actingAs($user)->test(Security::class)
            ->set('disableTwoFactorCode', $wrongCode)
            ->call('disableTwoFactor')
            ->assertHasErrors('disableTwoFactorCode');

        $this->assertSame($secret, $user->fresh()->tfa_secret);

        Livewire::actingAs($user)->test(Security::class)
            ->set('disableTwoFactorCode', $tfa->getCode($secret))
            ->call('disableTwoFactor')
            ->assertHasNoErrors()
            ->assertSet('twoFactorEnabled', false);

        $this->assertNull($user->fresh()->tfa_secret);
    }

    public function test_disabling_two_factor_refuses_a_used_code()
    {
        $tfa = new TwoFactorAuth(new EndroidQrCodeProvider, config('app.name'));
        $secret = $tfa->createSecret();
        $user = User::factory()->create(['tfa_secret' => $secret]);
        $code = $tfa->getCode($secret);

        Cache::put('tfa_used_code_' . $user->id . '_' . $code, true, 60);

        Livewire::actingAs($user)->test(Security::class)
            ->set('disableTwoFactorCode', $code)
            ->call('disableTwoFactor')
            ->assertHasErrors('disableTwoFactorCode');

        $this->assertSame($secret, $user->fresh()->tfa_secret);
    }

    public function test_two_factor_code_guesses_are_limited()
    {
        $tfa = new TwoFactorAuth(new EndroidQrCodeProvider, config('app.name'));
        $secret = $tfa->createSecret();
        $user = User::factory()->create(['tfa_secret' => $secret]);

        for ($i = 0; $i < 5; $i++) {
            Livewire::actingAs($user)->test(Security::class)
                ->set('disableTwoFactorCode', 'abcdef')
                ->call('disableTwoFactor')
                ->assertHasErrors('disableTwoFactorCode');
        }

        Livewire::actingAs($user)->test(Security::class)
            ->set('disableTwoFactorCode', $tfa->getCode($secret))
            ->call('disableTwoFactor')
            ->assertHasErrors('disableTwoFactorCode');

        $this->assertSame($secret, $user->fresh()->tfa_secret);
    }
}
