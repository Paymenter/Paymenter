<?php

namespace Tests\Feature\Invoices;

use App\Helpers\ExtensionHelper;
use App\Livewire\Invoices\Show;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CancelledInvoiceCreditsTest extends TestCase
{
    use RefreshDatabase;

    private function pendingInvoice(User $user, float $price = 100.00, ?Service $service = null): Invoice
    {
        $invoice = Invoice::factory()->create(['user_id' => $user->id, 'status' => Invoice::STATUS_PENDING]);

        $invoice->items()->create([
            'description' => 'Test Item',
            'quantity' => 1,
            'price' => $price,
            'reference_type' => $service ? Service::class : null,
            'reference_id' => $service?->id,
        ]);

        return $invoice->fresh();
    }

    private function applyCredits(User $user, Invoice $invoice): void
    {
        Livewire::actingAs($user)
            ->test(Show::class, ['invoice' => $invoice])
            ->set('selectedMethod', 'credit')
            ->call('processPayment');
    }

    public function test_credits_applied_to_an_invoice_are_returned_when_it_is_cancelled(): void
    {
        $user = User::factory()->create();
        $user->credits()->create(['currency_code' => 'USD', 'amount' => 40.00]);
        $invoice = $this->pendingInvoice($user);

        $this->applyCredits($user, $invoice);

        $this->assertEquals(0, $user->credits()->first()->amount);
        $this->assertEquals(60, $invoice->fresh()->remaining);

        $invoice->fresh()->update(['status' => Invoice::STATUS_CANCELLED, 'cancellation_reason' => 'Test']);

        $this->assertEquals(40, $user->credits()->first()->amount);
        $this->assertEquals(0, $invoice->fresh()->remaining);

        // Reopening and cancelling again does not return the same credits twice.
        $invoice->fresh()->update(['status' => Invoice::STATUS_PENDING]);
        $invoice->fresh()->update(['status' => Invoice::STATUS_CANCELLED]);

        $this->assertEquals(40, $user->credits()->first()->amount);
    }

    public function test_credits_are_returned_when_an_unpaid_order_is_cancelled_by_the_cron_job(): void
    {
        config(['settings.cronjob_order_cancel' => 7]);

        $user = User::factory()->create();
        $user->credits()->create(['currency_code' => 'USD', 'amount' => 4.00]);
        $product = $this->createProduct();
        $service = Service::factory()->create([
            'user_id' => $user->id,
            'plan_id' => $product->plan->id,
            'product_id' => $product->product->id,
            'status' => 'pending',
            'currency_code' => 'USD',
            'price' => 10.00,
            'created_at' => now()->subDays(8),
        ]);
        $invoice = $this->pendingInvoice($user, 10.00, $service);

        $this->applyCredits($user, $invoice);
        $this->assertEquals(0, $user->credits()->first()->amount);

        $this->artisan('app:cron-job')->assertExitCode(0);

        $this->assertEquals(Invoice::STATUS_CANCELLED, $invoice->fresh()->status);
        $this->assertEquals(4, $user->credits()->first()->amount);
    }

    public function test_cancelling_leaves_gateway_payments_and_paid_invoices_alone(): void
    {
        $user = User::factory()->create();
        $user->credits()->create(['currency_code' => 'USD', 'amount' => 0]);

        // A gateway payment is not credit and is not put on the balance.
        $pending = $this->pendingInvoice($user);
        ExtensionHelper::addPayment($pending->id, null, 30.00, transactionId: 'ext-1');
        $pending->fresh()->update(['status' => Invoice::STATUS_CANCELLED]);
        $this->assertEquals(0, $pending->transactions()->first()->refunded_amount);

        // An invoice that was paid in full with credits keeps its payment.
        $paid = $this->pendingInvoice($user, 20.00);
        ExtensionHelper::addPayment($paid->id, null, amount: 20.00, isCreditTransaction: true);
        $this->assertEquals(Invoice::STATUS_PAID, $paid->fresh()->status);
        $paid->fresh()->update(['status' => Invoice::STATUS_CANCELLED]);

        $this->assertEquals(0, $user->credits()->first()->amount);
    }
}
