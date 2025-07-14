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
}
