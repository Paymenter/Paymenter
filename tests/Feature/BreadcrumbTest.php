<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreadcrumbTest extends TestCase
{
    use RefreshDatabase;

    public function test_breadcrumb_renders_more_than_once_in_one_process(): void
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $this->actingAs($user)->withSession($this->loginUser($user))->get(route('account'))->assertOk();
        $this->actingAs($user)->withSession($this->loginUser($user))->get(route('account.security'))->assertOk();
    }
}
