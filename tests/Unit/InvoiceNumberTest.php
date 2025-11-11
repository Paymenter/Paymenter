<?php

namespace Tests\Unit;

use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceNumberTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic unit test example.
     */
    public function test_invoice_number_generation(): void
    {
        $user = \App\Models\User::factory()->create();
        // Generate first invoice
        config(['settings.invoice_number' => 1000]);

        $invoice = new Invoice;
        $invoice->user_id = $user->id;
        $invoice->currency_code = 'USD';
        $invoice->save();

        $this->assertEquals(1001, $invoice->number);

        // Config should be set to 1001
        $this->assertEquals(1001, config('settings.invoice_number'));

        // Do it once more for good measure
        $invoice2 = new Invoice;
        $invoice2->user_id = $user->id;
        $invoice2->currency_code = 'USD';
        $invoice2->save();

        $this->assertEquals(1002, $invoice2->number);

        // Config should be set to 1002
        $this->assertEquals(1002, config('settings.invoice_number'));
    }

    public function test_invoice_number_generation_proforma(): void
    {
        $user = \App\Models\User::factory()->create();
        // Generate first invoice
        config(['settings.invoice_number' => 2000]);
        config(['settings.invoice_proforma' => true]);

        $invoice = new Invoice;
        $invoice->user_id = $user->id;
        $invoice->currency_code = 'USD';
        $invoice->save();

        $invoice->items()->create([
            'name' => 'Test item',
            'price' => 10,
            'quantity' => 1,
        ]);

        // Number should not be set
        $this->assertNull($invoice->number);

        // Config should still be 2000
        $this->assertEquals(2000, config('settings.invoice_number'));

        // Add paid transaction
        $invoice->transactions()->create([
            'amount' => 10,
        ]);

        // Refresh invoice
        $invoice->refresh();

        // Number should now be set to 2001
        $this->assertEquals(2001, $invoice->number);

        // Config should be set to 2001
        $this->assertEquals(2001, config('settings.invoice_number'));
    }
}
