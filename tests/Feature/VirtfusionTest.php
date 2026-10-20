<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Paymenter\Extensions\Servers\Virtfusion\Virtfusion;
use Tests\TestCase;

class VirtfusionTest extends TestCase
{
    use RefreshDatabase;

    private Virtfusion $virtfusion;

    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->virtfusion = new Virtfusion(['host' => 'https://vf.test', 'apikey' => 'key']);
        $this->service = new Service;
        $this->service->setRelation('user', User::factory()->create());
    }

    private function assertUserNotCreated(): void
    {
        Http::assertNotSent(fn (Request $request) => $request->method() === 'POST');
    }

    public function test_existing_user_is_returned(): void
    {
        Http::fake(['vf.test/api/v1/users/*/byExtRelation' => Http::response(['data' => ['id' => 42]])]);

        $this->assertSame('42', $this->virtfusion->getUser($this->service));
        $this->assertUserNotCreated();
    }

    public function test_missing_user_is_created(): void
    {
        Http::fake([
            'vf.test/api/v1/users/*/byExtRelation' => Http::response(['msg' => 'not found'], 404),
            'vf.test/api/v1/users' => Http::response(['data' => ['id' => 43]], 201),
        ]);

        $this->assertSame('43', $this->virtfusion->getUser($this->service));
        Http::assertSent(fn (Request $request) => $request->method() === 'POST' && $request['extRelationId'] === $this->service->user->id);
    }

    public function test_server_error_on_lookup_does_not_create_a_user(): void
    {
        Http::fake(['vf.test/api/v1/users/*/byExtRelation' => Http::response(['msg' => 'Server Error'], 500)]);

        try {
            $this->virtfusion->getUser($this->service);
            $this->fail('No exception was thrown');
        } catch (Exception $e) {
            $this->assertStringContainsString('got status code 500', $e->getMessage());
            $this->assertStringContainsString('Server Error', $e->getMessage());
        }

        $this->assertUserNotCreated();
    }

    public function test_timeout_on_lookup_does_not_create_a_user(): void
    {
        Http::fake(['vf.test/api/v1/users/*/byExtRelation' => fn () => throw new ConnectionException('cURL error 28: Operation timed out')]);

        $this->expectException(ConnectionException::class);

        try {
            $this->virtfusion->getUser($this->service);
        } finally {
            $this->assertUserNotCreated();
        }
    }

    public function test_failed_creation_reports_the_api_error(): void
    {
        Http::fake([
            'vf.test/api/v1/users/*/byExtRelation' => Http::response([], 404),
            'vf.test/api/v1/users' => Http::response(['errors' => ['email' => ['The email has already been taken.']]], 422),
        ]);

        $this->expectExceptionMessage('email: The email has already been taken.');

        $this->virtfusion->getUser($this->service);
    }

    public function test_response_without_a_user_id_is_an_error(): void
    {
        Http::fake(['vf.test/api/v1/users/*/byExtRelation' => Http::response(['data' => []])]);

        $this->expectExceptionMessage('did not return a user id');

        $this->virtfusion->getUser($this->service);
    }
}
