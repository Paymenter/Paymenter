<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LockSessionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Define a test route to apply the middleware
        Route::middleware('web')->get('/', function () {
            return 'OK';
        });
    }

    public function test_ip_is_stored_on_first_request_and_allows_same_ip()
    {
        Config::set('settings.session_validation', 'ip_both');

        $user = User::factory()->create(['role_id' => 1]); // admin

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->withSession($this->loginUser($user))
            ->get('/')
            ->assertOk();

        // Second request with same IP should still be OK
        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->withSession(session()->all())
            ->get('/')
            ->assertOk();
    }

    public function test_ip_change_triggers_logout_and_redirect_to_login()
    {
        Config::set('settings.session_validation', 'ip_both');

        $user = User::factory()->create(['role_id' => 1]); // admin

        // First request from initial IP
        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->withSession($this->loginUser($user))
            ->get('/')
            ->assertOk();

        // Second request from different IP should invalidate session
        $this->withServerVariables(['REMOTE_ADDR' => '5.6.7.8'])
            ->withSession(session()->all())
            ->get('/')
            ->assertRedirect('/');
    }

    public function test_client_role_respects_ip_client_setting()
    {
        Config::set('settings.session_validation', 'ip_client');

        $user = User::factory()->create(['role_id' => null]); // client

        $this->withServerVariables(['REMOTE_ADDR' => '9.9.9.9'])
            ->withSession($this->loginUser($user))
            ->get('/')
            ->assertOk();

        // Change client IP -> should redirect (locked)
        $this->withServerVariables(['REMOTE_ADDR' => '9.9.9.8'])
            ->withSession(session()->all())
            ->get('/')
            ->assertRedirect('/');
    }

    public function test_user_agent_is_stored_and_allows_same_ua_for_admin()
    {
        Config::set('settings.session_validation', 'user_agent_admin');

        $user = User::factory()->create(['role_id' => 1]); // admin

        $ua1 = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)';

        $this->withHeaders(['User-Agent' => $ua1])
            ->withSession($this->loginUser($user))
            ->get('/')
            ->assertOk();

        // Same UA should still be OK
        $this->withHeaders(['User-Agent' => $ua1])
            ->withSession(session()->all())
            ->get('/')
            ->assertOk();
    }

    public function test_user_agent_change_triggers_redirect_for_admin()
    {
        Config::set('settings.session_validation', 'user_agent_admin');

        $user = User::factory()->create(['role_id' => 1]); // admin

        $ua1 = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)';
        $ua2 = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)';

        $this->withHeaders(['User-Agent' => $ua1])
            ->withSession($this->loginUser($user))
            ->get('/')
            ->assertOk();

        // Change UA -> should redirect
        $this->withHeaders(['User-Agent' => $ua2])
            ->withSession(session()->all())
            ->get('/')
            ->assertRedirect('/');
    }

    public function test_user_agent_client_mode_respects_client_role()
    {
        Config::set('settings.session_validation', 'user_agent_client');

        $user = User::factory()->create(['role_id' => null]); // client

        $ua1 = 'Mozilla/5.0 (X11; Linux x86_64)';
        $ua2 = 'Mozilla/5.0 (X11; Linux x86_64; rv:120)';

        $this->withHeaders(['User-Agent' => $ua1])
            ->withSession($this->loginUser($user))
            ->get('/')
            ->assertOk();

        // Change UA -> should redirect
        $this->withHeaders(['User-Agent' => $ua2])
            ->withSession(session()->all())
            ->get('/')
            ->assertRedirect('/');
    }

    public function test_no_validation_setting_allows_all_requests()
    {
        Config::set('settings.session_validation', 'none');

        $user = User::factory()->create(['role_id' => 1]); // admin

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->withHeaders(['User-Agent' => 'TestAgent/1.0'])
            ->withSession($this->loginUser($user))
            ->get('/')
            ->assertOk();

        // Change both IP and UA -> should still be OK
        $this->withServerVariables(['REMOTE_ADDR' => '5.6.7.8'])
            ->withHeaders(['User-Agent' => 'AnotherAgent/2.0'])
            ->withSession(session()->all())
            ->get('/')
            ->assertOk();
    }

    public function test_both_ip_and_user_agent_validation()
    {
        Config::set('settings.session_validation', 'ip_user_agent_both');

        $user = User::factory()->create(['role_id' => null]); // client

        $ua1 = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)';

        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->withHeaders(['User-Agent' => $ua1])
            ->withSession($this->loginUser($user))
            ->get('/')
            ->assertOk();

        // Change only IP -> should redirect
        $this->withServerVariables(['REMOTE_ADDR' => '5.6.7.8'])
            ->withHeaders(['User-Agent' => $ua1])
            ->withSession(session()->all())
            ->get('/')
            ->assertRedirect('/');

        // Reset to original IP and UA
        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->withHeaders(['User-Agent' => $ua1])
            ->withSession($this->loginUser($user))
            ->get('/')
            ->assertOk();

        // Change only UA -> should redirect
        $this->withServerVariables(['REMOTE_ADDR' => '1.2.3.4'])
            ->withHeaders(['User-Agent' => 'DifferentAgent/3.0'])
            ->withSession(session()->all())
            ->get('/')
            ->assertRedirect('/');
    }
}
