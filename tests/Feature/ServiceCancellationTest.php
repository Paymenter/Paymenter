<?php

namespace Tests\Feature;

use App\Livewire\Services\Cancel;
use App\Livewire\Services\Show;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\ServiceCancellation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceCancellationTest extends TestCase
{
    use RefreshDatabase;

    private function createService(array $attributes = []): Service
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        return Service::factory()->create(array_merge([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => Service::STATUS_ACTIVE,
            'expires_at' => now()->addDays(10),
            'currency_code' => 'USD',
            'price' => 10.00,
        ], $attributes));
    }

    public function test_service_can_be_cancelled_at_end_of_period(): void
    {
        $service = $this->createService();

        Livewire::actingAs($service->user)
            ->test(Cancel::class, ['service' => $service])
            ->set('type', 'end_of_period')
            ->set('reason', 'No longer needed')
            ->call('cancelService')
            ->assertRedirect(route('services.show', $service));

        $this->assertDatabaseHas('service_cancellations', [
            'service_id' => $service->id,
            'type' => 'end_of_period',
        ]);
        $this->assertEquals(Service::STATUS_ACTIVE, $service->fresh()->status);
    }

    public function test_service_cannot_be_cancelled_twice(): void
    {
        $service = $this->createService();
        ServiceCancellation::create([
            'service_id' => $service->id,
            'type' => 'end_of_period',
            'reason' => 'First request',
        ]);

        Livewire::actingAs($service->user)
            ->test(Cancel::class, ['service' => $service])
            ->set('reason', 'Second request')
            ->call('cancelService')
            ->assertRedirect(route('services.show', $service));

        $this->assertEquals(1, ServiceCancellation::where('service_id', $service->id)->count());
    }

    public function test_cancelled_service_cannot_be_cancelled(): void
    {
        $service = $this->createService(['status' => Service::STATUS_CANCELLED]);

        Livewire::actingAs($service->user)
            ->test(Cancel::class, ['service' => $service])
            ->set('reason', 'Again')
            ->call('cancelService');

        $this->assertDatabaseMissing('service_cancellations', ['service_id' => $service->id]);
    }

    public function test_pending_order_is_cancelled_immediately(): void
    {
        $service = $this->createService([
            'status' => Service::STATUS_PENDING,
            'expires_at' => null,
        ]);
        $invoice = Invoice::factory()->create([
            'user_id' => $service->user_id,
            'status' => Invoice::STATUS_PENDING,
            'currency_code' => 'USD',
        ]);
        $invoice->items()->create([
            'reference_id' => $service->id,
            'reference_type' => Service::class,
            'price' => 10.00,
            'quantity' => 1,
            'description' => 'Test',
        ]);

        Livewire::actingAs($service->user)
            ->test(Cancel::class, ['service' => $service])
            ->assertSet('type', 'immediate')
            ->assertDontSee(__('services.cancel_end_of_period'))
            // A stale form may still submit end_of_period
            ->set('type', 'end_of_period')
            ->set('reason', 'Changed my mind')
            ->call('cancelService')
            ->assertRedirect(route('services.show', $service));

        $this->assertDatabaseHas('service_cancellations', [
            'service_id' => $service->id,
            'type' => 'immediate',
        ]);
        $this->assertEquals(Service::STATUS_CANCELLED, $service->fresh()->status);
        $this->assertEquals(Invoice::STATUS_CANCELLED, $invoice->fresh()->status);
    }

    public function test_cancel_query_parameter_is_ignored_for_service_that_cannot_be_cancelled(): void
    {
        $service = $this->createService(['status' => Service::STATUS_CANCELLED]);

        Livewire::actingAs($service->user)
            ->withQueryParams(['cancel' => true])
            ->test(Show::class, ['service' => $service])
            ->assertSet('showCancel', false);
    }
}
