<?php

namespace Tests\Unit;

use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
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
        $user = User::factory()->create();
        // Generate first invoice
        Setting::updateOrCreate(['key' => 'invoice_number'], ['value' => 1000]);
        Setting::updateOrCreate(['key' => 'invoice_number_format'], ['value' => '{number}']);

        $invoice = new Invoice;
        $invoice->user_id = $user->id;
        $invoice->currency_code = 'USD';
        $invoice->save();

        $this->assertEquals(1001, $invoice->number);

        $this->assertDatabaseHas('settings', [
            'key' => 'invoice_number',
            'value' => 1001,
        ]);

        // Do it once more for good measure
        $invoice2 = new Invoice;
        $invoice2->user_id = $user->id;
        $invoice2->currency_code = 'USD';
        $invoice2->save();

        $this->assertEquals(1002, $invoice2->number);

        // Config should be set to 1002
        $this->assertDatabaseHas('settings', [
            'key' => 'invoice_number',
            'value' => 1002,
        ]);
    }

    public function test_invoice_number_generation_proforma(): void
    {
        $user = User::factory()->create();
        // Generate first invoice
        Setting::updateOrCreate(['key' => 'invoice_number'], ['value' => 2000]);
        Setting::updateOrCreate(['key' => 'invoice_number_format'], ['value' => '{number}']);
        Setting::updateOrCreate(['key' => 'invoice_proforma'], ['value' => true]);

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
