<?php

namespace Tests\Feature\Invoices;

use App\Classes\Extension\Gateway as GatewayExtension;
use App\Enums\InvoiceTransactionStatus;
use App\Exceptions\DisplayException;
use App\Helpers\ExtensionHelper;
use App\Livewire\Invoices\Show;
use App\Models\BillingAgreement;
use App\Models\Gateway;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use RuntimeException;
use Tests\TestCase;
use Throwable;

class SavedMethodChargeTest extends TestCase
{
    use RefreshDatabase;

    private const EXTENSION = 'SavedMethodChargeFake';

    private User $user;

    private Invoice $invoice;

    private BillingAgreement $agreement;

    protected function setUp(): void
    {
        parent::setUp();

        $class = $this->fake();

        if (!class_exists($class)) {
            $fake = new class extends GatewayExtension
            {
                public static int $charges = 0;

                public static mixed $result = true;

                public function pay($invoice, $total) {}

                public function supportsBillingAgreements(): bool
                {
                    return true;
                }

                public function charge(Invoice $invoice, $total, BillingAgreement $billingAgreement)
                {
                    self::$charges++;

                    if (self::$result instanceof Throwable) {
                        throw self::$result;
                    }

                    return self::$result;
                }
            };

            class_alias(get_class($fake), $class);
        }

        $class::$charges = 0;
        $class::$result = true;

        $gateway = Gateway::create(['name' => 'Fake', 'extension' => self::EXTENSION, 'type' => 'gateway', 'enabled' => true]);

        $this->user = User::factory()->create();
        $this->invoice = Invoice::factory()->create(['user_id' => $this->user->id, 'status' => Invoice::STATUS_PENDING]);
        $this->invoice->items()->create(['description' => 'Test Item', 'quantity' => 1, 'price' => 10.00]);
        $this->invoice = $this->invoice->fresh();

        $this->agreement = BillingAgreement::create([
            'user_id' => $this->user->id,
            'gateway_id' => $gateway->id,
            'name' => 'Visa **** 4242',
            'external_reference' => 'pm_test',
        ]);
    }

    private function fake(): string
    {
        return 'Paymenter\\Extensions\\Gateways\\' . self::EXTENSION . '\\' . self::EXTENSION;
    }

    private function charge(): bool
    {
        return ExtensionHelper::charge($this->agreement->gateway, $this->invoice, $this->agreement);
    }

    public function test_second_charge_is_refused_until_the_first_settles()
    {
        $this->assertTrue($this->charge());

        try {
            $this->charge();
            $this->fail('The second charge was not refused.');
        } catch (DisplayException $e) {
            $this->assertSame(__('invoices.charge_in_progress'), $e->getMessage());
        }

        $this->assertSame(1, $this->fake()::$charges);
    }

    public function test_failed_charge_can_be_retried_immediately()
    {
        $this->fake()::$result = false;
        $this->assertFalse($this->charge());

        $this->fake()::$result = new RuntimeException('declined');

        try {
            $this->charge();
        } catch (RuntimeException) {
        }

        $this->fake()::$result = true;
        $this->assertTrue($this->charge());

        $this->assertSame(3, $this->fake()::$charges);
    }

    public function test_invoice_with_a_processing_payment_is_not_charged()
    {
        $this->invoice->transactions()->create(['amount' => 10.00, 'status' => InvoiceTransactionStatus::Processing]);

        $this->expectException(DisplayException::class);

        try {
            $this->charge();
        } finally {
            $this->assertSame(0, $this->fake()::$charges);
        }
    }

    public function test_double_submit_on_invoice_page_charges_once()
    {
        $component = Livewire::actingAs($this->user)
            ->test(Show::class, ['invoice' => $this->invoice])
            ->set('selectedMethod', $this->agreement->ulid);

        $component->call('processPayment');
        $component->call('processPayment');

        $this->assertSame(1, $this->fake()::$charges);
    }
}
