<?php

namespace Tests\Feature\Invoices;

use App\Enums\InvoiceTransactionStatus;
use App\Livewire\Invoices\Show;
use App\Models\Gateway;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceGatewaysTest extends TestCase
{
    use RefreshDatabase;

    private const GATEWAY = 'Paymenter\\Extensions\\Gateways\\TestRecordingGateway\\TestRecordingGateway';

    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists(self::GATEWAY)) {
            $gateway = new class
            {
                public static array $asked = [];

                public function __construct($config = []) {}

                public function canUseGateway($total, $currency, $type, $items = [])
                {
                    self::$asked[] = $total;

                    return true;
                }
            };
            class_alias($gateway::class, self::GATEWAY);
        }
        (self::GATEWAY)::$asked = [];

        Gateway::create(['name' => 'Test', 'extension' => 'TestRecordingGateway', 'type' => 'gateway', 'enabled' => true]);
    }

    public function test_gateways_are_asked_about_the_remaining_balance(): void
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id, 'status' => Invoice::STATUS_PENDING]);
        $invoice->items()->create(['description' => 'Test Item', 'quantity' => 1, 'price' => 100.00]);
        // Part of the invoice is already paid, e.g. with account credit
        $invoice->transactions()->create(['amount' => 60.00, 'status' => InvoiceTransactionStatus::Succeeded]);

        $invoice = $invoice->fresh();
        $this->assertSame(Invoice::STATUS_PENDING, $invoice->status);

        $gateways = Livewire::actingAs($user)->test(Show::class, ['invoice' => $invoice])->instance()->gateways;

        $this->assertCount(1, $gateways);
        $this->assertEquals([40.00], (self::GATEWAY)::$asked);
    }
}
