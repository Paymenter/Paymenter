<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class RememberMeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth'])->get('/remember-me-test', function () {
            return 'OK';
        });
    }

    public function test_remember_cookie_logs_in_before_auth_middleware_runs()
    {
        $user = User::factory()->create();

        $userSession = UserSession::create([
            'user_id' => $user->id,
            'last_activity' => now(),
            'expires_at' => now()->addDays(30),
        ]);

        // No session at all, as after the session cookie has expired
        $this->withCookie('paymenter_remember', $userSession->ulid)
            ->get('/remember-me-test')
            ->assertOk()
            ->assertSee('OK');

        $this->assertAuthenticatedAs($user);
    }

    public function test_expired_remember_cookie_redirects_to_login()
    {
        $user = User::factory()->create();

        $userSession = UserSession::create([
            'user_id' => $user->id,
            'last_activity' => now()->subDays(31),
            'expires_at' => now()->subDay(),
        ]);

        $this->withCookie('paymenter_remember', $userSession->ulid)
            ->get('/remember-me-test')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
