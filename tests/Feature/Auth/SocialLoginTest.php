<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request as PsrRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    private function assertFailedWith(string $key): void
    {
        $this->assertEquals(
            ['message' => __($key), 'type' => 'error'],
            session('notification')
        );
    }

    private function fakeProvider(callable $user): void
    {
        $provider = Mockery::mock(GithubProvider::class);
        $provider->shouldReceive('user')->andReturnUsing($user);

        Socialite::shouldReceive('buildProvider')->andReturn($provider);
    }

    public function test_cancelled_consent_redirects_to_login()
    {
        $this->get('/oauth/github/callback?error=access_denied')
            ->assertRedirect(route('login'));

        $this->assertFailedWith('auth.oauth.cancelled');
        $this->assertGuest();
    }

    public function test_missing_state_redirects_to_login()
    {
        // A reloaded callback: the state was already pulled from the session
        $this->get('/oauth/github/callback?code=abc&state=xyz')
            ->assertRedirect(route('login'));

        $this->assertFailedWith('auth.oauth.expired');
        $this->assertGuest();
    }

    public function test_failed_token_exchange_redirects_to_login()
    {
        $this->fakeProvider(function () {
            throw new ConnectException('Connection refused', new PsrRequest('POST', 'https://github.com/login/oauth/access_token'));
        });

        $this->get('/oauth/github/callback?code=abc&state=xyz')
            ->assertRedirect(route('login'));

        $this->assertFailedWith('auth.oauth.failed');
        $this->assertGuest();
    }

    public function test_account_without_email_redirects_to_login()
    {
        $this->fakeProvider(fn () => (new SocialiteUser)->setRaw([])->map(['email' => null]));

        $this->get('/oauth/github/callback?code=abc&state=xyz')
            ->assertRedirect(route('login'));

        $this->assertFailedWith('auth.oauth.no_email');
        $this->assertGuest();
    }

    public function test_unregistered_email_redirects_to_register()
    {
        $this->fakeProvider(fn () => (new SocialiteUser)->setRaw([])->map(['email' => 'nobody@paymenter.org']));

        $this->get('/oauth/github/callback?code=abc&state=xyz')
            ->assertRedirect(route('register'));

        $this->assertFailedWith('auth.oauth.account_not_registered');
        $this->assertGuest();
    }

    public function test_registered_email_logs_in()
    {
        $user = User::factory()->create(['email' => 'tests@paymenter.org']);

        $this->fakeProvider(fn () => (new SocialiteUser)->setRaw([])->map(['email' => 'tests@paymenter.org']));

        $this->get('/oauth/github/callback?code=abc&state=xyz')
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }
}
