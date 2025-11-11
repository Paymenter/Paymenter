<?php

namespace Tests\Feature\Invoices;

use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePaymentProcessingTest extends TestCase
{
    use RefreshDatabase;

    private function createInvoiceWithItem($total = 100.00)
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id]);

        $invoice->items()->create([
            'description' => 'Test Item',
            'quantity' => 1,
            'price' => $total,
        ]);

        return $invoice->fresh();
    }

    public function test_invoice_starts_with_pending_status()
    {
        $invoice = $this->createInvoiceWithItem();

        $this->assertEquals('pending', $invoice->status);
        $this->assertGreaterThan(0, $invoice->total);
    }

    public function test_invoice_calculates_remaining_amount_correctly()
    {
        $invoice = $this->createInvoiceWithItem(100.00);

        // No payments yet
        $this->assertEquals(100.00, $invoice->remaining);

        // Add partial payment
        $invoice->transactions()->create([
            'amount' => 30.00,
            'status' => \App\Enums\InvoiceTransactionStatus::Succeeded,
        ]);

        $this->assertEquals(70.00, $invoice->fresh()->remaining);
    }

    public function test_successful_payment_marks_invoice_as_paid()
    {
        $invoice = $this->createInvoiceWithItem(100.00);

        // Process full payment
        \App\Helpers\ExtensionHelper::addPayment(
            $invoice->id,
            'TestGateway',
            100.00,
            fee: 2.50,
            transactionId: 'test_txn_123'
        );

        $invoice->refresh();

        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0, $invoice->remaining);
        $this->assertEquals(1, $invoice->transactions()->count());

        $transaction = $invoice->transactions()->first();
        $this->assertEquals(100.00, $transaction->amount);
        $this->assertEquals(2.50, $transaction->fee);
        $this->assertEquals('test_txn_123', $transaction->transaction_id);
    }

    public function test_partial_payment_keeps_invoice_pending()
    {
        $invoice = $this->createInvoiceWithItem(100.00);

        // Process partial payment
        \App\Helpers\ExtensionHelper::addPayment($invoice->id, 'TestGateway', 60.00);

        $invoice->refresh();

        $this->assertEquals('pending', $invoice->status);
        $this->assertEquals(40.00, $invoice->remaining);
    }

    public function test_overpayment_marks_invoice_as_paid()
    {
        $invoice = $this->createInvoiceWithItem(100.00);

        // Process overpayment
        \App\Helpers\ExtensionHelper::addPayment($invoice->id, 'TestGateway', 150.00);

        $invoice->refresh();

        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(-50.00, $invoice->remaining); // Credit balance
    }

    public function test_service_is_activated_when_invoice_is_paid()
    {
        $user = User::factory()->create();
        $product = $this->createProduct();

        $service = Service::factory()->create(['status' => 'pending', 'user_id' => $user->id, 'product_id' => $product->product->id, 'plan_id' => $product->plan->id]);
        $invoice = Invoice::factory()->create([
            'user_id' => $service->user_id,
            'status' => 'pending',
            'due_at' => now()->addDays(7),
            'currency_code' => $service->currency_code,
        ]);

        // Link service to invoice
        $invoice->items()->create([
            'reference_type' => Service::class,
            'reference_id' => $service->id,
            'price' => 100.00,
            'quantity' => 1,
            'description' => 'blah',
        ]);

        // Pay the invoice
        \App\Helpers\ExtensionHelper::addPayment($invoice->id, 'Stripe', 100.00);

        $service->refresh();

        $this->assertEquals('active', $service->status);
        $this->assertNotNull($service->expires_at);
    }

    public function test_invoice_handles_multiple_partial_payments()
    {
        $invoice = $this->createInvoiceWithItem(100.00);

        // First payment
        \App\Helpers\ExtensionHelper::addPayment($invoice->id, 'Stripe', 30.00);

        // Second payment
        \App\Helpers\ExtensionHelper::addPayment($invoice->id, 'PayPal', 40.00);

        // Third payment completes it
        \App\Helpers\ExtensionHelper::addPayment($invoice->id, 'Stripe', 30.00);

        $invoice->refresh();

        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0, $invoice->remaining);
        $this->assertEquals(3, $invoice->transactions()->count());
    }

    public function test_payment_fee_is_recorded_correctly()
    {
        $invoice = $this->createInvoiceWithItem(100.00);

        \App\Helpers\ExtensionHelper::addPayment(
            $invoice->id,
            'Stripe',
            100.00,
            fee: 3.20,
            transactionId: 'pi_123'
        );

        $transaction = $invoice->transactions()->first();

        $this->assertEquals(3.20, $transaction->fee);
    }

    public function test_fee_can_be_updated_after_payment()
    {
        $invoice = $this->createInvoiceWithItem(100.00);

        // Initial payment without fee
        \App\Helpers\ExtensionHelper::addPayment(
            $invoice->id,
            'Stripe',
            100.00,
            transactionId: 'pi_123'
        );

        $transaction = $invoice->transactions()->first();
        $this->assertEquals(0, $transaction->fee);

        // Update fee (from Stripe webhook)
        \App\Helpers\ExtensionHelper::addPaymentFee('pi_123', 2.90);

        $transaction = $invoice->transactions()->first();
        $this->assertEquals(2.90, $transaction->fee);
    }
}
