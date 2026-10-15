<?php

namespace Tests\Feature;

use App\Classes\Extension\Server as ServerExtension;
use App\Livewire\Services\Show;
use App\Models\Server;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceShowTest extends TestCase
{
    use RefreshDatabase;

    private const EXTENSION = 'ServiceShowTestServer';

    protected function setUp(): void
    {
        parent::setUp();

        // ExtensionHelper resolves server extensions by this class name
        $class = 'Paymenter\Extensions\Servers\ServiceShowTestServer\ServiceShowTestServer';
        if (!class_exists($class)) {
            class_alias(ServiceShowTestServer::class, $class);
        }
        ServiceShowTestServer::$called = false;
    }

    private function createService(): Service
    {
        $server = Server::create([
            'name' => 'Test Server',
            'extension' => self::EXTENSION,
            'type' => 'server',
            'enabled' => true,
        ]);
        $product = $this->createProduct(['server_id' => $server->id]);

        return Service::factory()->create([
            'user_id' => User::factory()->create()->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => Service::STATUS_ACTIVE,
            'expires_at' => now()->addDays(10),
            'currency_code' => 'USD',
            'price' => 10.00,
        ]);
    }

    public function test_actions_work_while_service_is_active(): void
    {
        $service = $this->createService();

        Livewire::actingAs($service->user)
            ->test(Show::class, ['service' => $service])
            ->assertSee('fake-extension-view')
            ->call('goto', 'login')
            ->assertRedirect('https://example.com/login');

        $this->assertTrue(ServiceShowTestServer::$called);
    }

    public function test_actions_are_refused_after_service_is_suspended(): void
    {
        $service = $this->createService();

        $component = Livewire::actingAs($service->user)
            ->test(Show::class, ['service' => $service])
            ->assertSee('fake-extension-view');

        $service->update(['status' => Service::STATUS_SUSPENDED]);

        $component->call('goto', 'login')
            ->assertNoRedirect()
            ->assertDontSee('fake-extension-view');

        $this->assertFalse(ServiceShowTestServer::$called);
    }

    public function test_updating_billing_agreement_without_selection_does_not_fail(): void
    {
        $service = $this->createService();

        Livewire::actingAs($service->user)
            ->test(Show::class, ['service' => $service])
            ->set('selectedMethod', 'not-a-real-agreement')
            ->call('updateBillingAgreement')
            ->assertDispatched('notify');

        $this->assertNull($service->fresh()->billing_agreement_id);
    }
}

class ServiceShowTestServer extends ServerExtension
{
    public static bool $called = false;

    public function getActions(Service $service, $settings, $properties)
    {
        return [
            ['type' => 'button', 'label' => 'Login', 'function' => 'login'],
            ['type' => 'view', 'label' => 'Details', 'name' => 'details'],
        ];
    }

    public function getView(Service $service, $settings, $properties, $view)
    {
        return '<p>fake-extension-view</p>';
    }

    public function login(Service $service, $settings, $properties)
    {
        self::$called = true;

        return 'https://example.com/login';
    }
}
