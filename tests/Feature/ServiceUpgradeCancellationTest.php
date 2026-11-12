<?php

namespace Tests\Feature;

use App\Livewire\Services\Show;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\ServiceUpgrade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ServiceUpgradeCancellationTest extends TestCase
{
    use RefreshDatabase;

    private function createUpgrade(?string $invoiceStatus = Invoice::STATUS_PENDING, ?\DateTimeInterface $createdAt = null): ServiceUpgrade
    {
        $product = $this->createProduct();

        $service = Service::factory()->create([
            'user_id' => User::factory()->create()->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => Service::STATUS_ACTIVE,
            'expires_at' => now()->addDays(20),
            'currency_code' => 'USD',
            'price' => 10.00,
        ]);

        $upgrade = ServiceUpgrade::create([
            'service_id' => $service->id,
            'product_id' => $product->product->id,
            'plan_id' => $product->plan->id,
            'status' => ServiceUpgrade::STATUS_PENDING,
        ]);

        if ($invoiceStatus) {
            $invoice = Invoice::factory()->create([
                'user_id' => $service->user_id,
                'status' => $invoiceStatus,
                'currency_code' => 'USD',
                'due_at' => now()->addDays(7),
            ]);
            $invoice->items()->create([
                'reference_id' => $upgrade->id,
                'reference_type' => ServiceUpgrade::class,
                'price' => 5.00,
                'quantity' => 1,
                'description' => 'Upgrade',
            ]);
            $upgrade->update(['invoice_id' => $invoice->id]);
        }

        if ($createdAt) {
            $upgrade->forceFill(['created_at' => $createdAt])->save();
        }

        return $upgrade->fresh();
    }

    public function test_customer_can_cancel_unpaid_upgrade(): void
    {
        $upgrade = $this->createUpgrade();

        Livewire::actingAs($upgrade->service->user)
            ->test(Show::class, ['service' => $upgrade->service])
            ->assertSee(__('services.upgrade_cancel'))
            ->call('cancelUpgrade')
            ->assertDontSee(__('services.upgrade_cancel'));

        $this->assertEquals(ServiceUpgrade::STATUS_CANCELLED, $upgrade->fresh()->status);
        $this->assertEquals(Invoice::STATUS_CANCELLED, $upgrade->invoice->fresh()->status);
    }

    public function test_paid_upgrade_cannot_be_cancelled(): void
    {
        $upgrade = $this->createUpgrade(Invoice::STATUS_PAID);

        $this->assertFalse($upgrade->cancel());

        $this->assertEquals(ServiceUpgrade::STATUS_PENDING, $upgrade->fresh()->status);
        $this->assertEquals(Invoice::STATUS_PAID, $upgrade->invoice->fresh()->status);
    }

    public function test_cron_cancels_unpaid_upgrades_after_configured_days(): void
    {
        config(['settings.cronjob_upgrade_cancel' => 2]);

        $stale = $this->createUpgrade(Invoice::STATUS_PENDING, now()->subDays(2)->subHour());
        $recent = $this->createUpgrade(Invoice::STATUS_PENDING, now()->subDays(2)->addHour());
        $withoutInvoice = $this->createUpgrade(null, now()->subDays(2)->subHour());

        $this->artisan('app:cron-job')->assertExitCode(0);

        $this->assertEquals(ServiceUpgrade::STATUS_CANCELLED, $stale->fresh()->status);
        $this->assertEquals(Invoice::STATUS_CANCELLED, $stale->invoice->fresh()->status);

        $this->assertEquals(ServiceUpgrade::STATUS_PENDING, $recent->fresh()->status);
        $this->assertEquals(Invoice::STATUS_PENDING, $recent->invoice->fresh()->status);

        $this->assertEquals(ServiceUpgrade::STATUS_CANCELLED, $withoutInvoice->fresh()->status);
    }
}
