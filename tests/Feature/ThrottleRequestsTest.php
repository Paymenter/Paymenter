<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ThrottleRequestsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('throttle:2,1')->get('/throttle-test', fn () => 'OK');
    }

    public function test_guest_ipv6_addresses_in_one_64_share_a_limit()
    {
        $this->withServerVariables(['REMOTE_ADDR' => '2001:db8::1'])->get('/throttle-test')->assertOk();
        $this->withServerVariables(['REMOTE_ADDR' => '2001:db8::2'])->get('/throttle-test')->assertOk();
        $this->withServerVariables(['REMOTE_ADDR' => '2001:db8::3'])->get('/throttle-test')->assertTooManyRequests();

        $this->withServerVariables(['REMOTE_ADDR' => '2001:db8:0:1::1'])->get('/throttle-test')->assertOk();
    }

    public function test_guest_ipv4_addresses_have_separate_limits()
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.1'])->get('/throttle-test')->assertOk();
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.1'])->get('/throttle-test')->assertOk();
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.1'])->get('/throttle-test')->assertTooManyRequests();

        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.2'])->get('/throttle-test')->assertOk();
    }
}
