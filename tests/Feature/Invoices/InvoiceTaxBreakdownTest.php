<?php

namespace Tests\Feature\Invoices;

use App\Enums\AdjustmentNoteType;
use App\Livewire\Invoices\Show;
use App\Models\Invoice;
use App\Models\TaxRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvoiceTaxBreakdownTest extends TestCase
{
    use RefreshDatabase;

    private function createInvoice(bool $withAdjustment = true): Invoice
    {
        config(['settings.tax_enabled' => true]);
        TaxRate::create(['name' => 'VAT', 'rate' => 20, 'country' => 'all']);

        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'user_id' => $user->id,
            'status' => Invoice::STATUS_PENDING,
        ]);
        $invoice->items()->create([
            'description' => 'Test item',
            'quantity' => 1,
            'price' => 120,
        ]);
        if ($withAdjustment) {
            // E.g. a partial refund
            $invoice->adjustmentNotes()->create([
                'type' => AdjustmentNoteType::Credit->value,
                'amount' => -60,
                'description' => 'Partial refund',
            ]);
        }

        return $invoice->fresh();
    }

    /**
     * The amounts shown after the "Subtotal" label, in order: subtotal, tax, [adjustments], total.
     */
    private function totalsLines(string $html): array
    {
        $totals = substr($html, strrpos($html, 'Subtotal'));
        preg_match_all('/\$(-?[\d,]+\.\d{2})/', $totals, $matches);

        return array_map(fn ($amount) => (float) str_replace(',', '', $amount), $matches[1]);
    }

    private function assertTotalsAddUp(string $html, array $expected): void
    {
        $lines = array_slice($this->totalsLines($html), 0, count($expected));

        $this->assertSame($expected, $lines);
        $total = array_pop($lines);
        $this->assertEqualsWithDelta($total, array_sum($lines), 0.001, 'Subtotal, tax and adjustments must add up to the total');
    }

    public function test_client_invoice_tax_breakdown_does_not_include_adjustment_notes(): void
    {
        $invoice = $this->createInvoice();

        $this->assertSame(60.0, $invoice->total);

        // Items: 100.00 + 20.00 VAT = 120.00, then -60.00 in adjustments, total 60.00
        $html = Livewire::test(Show::class, ['invoice' => $invoice])
            ->assertSeeInOrder(['Subtotal', '$100.00', 'VAT', '$20.00', 'Adjustments', '$-60.00', 'Total', '$60.00'])
            ->assertDontSee('$50.00')
            ->assertDontSee('$10.00')
            ->html();

        $this->assertTotalsAddUp($html, [100.0, 20.0, -60.0, 60.0]);
    }

    public function test_pdf_invoice_tax_breakdown_does_not_include_adjustment_notes(): void
    {
        $invoice = $this->createInvoice();

        $html = view('pdf.invoice', ['invoice' => $invoice])->render();

        $this->assertStringNotContainsString('$50.00', $html);
        $this->assertStringNotContainsString('$10.00', $html);
        $this->assertTotalsAddUp($html, [100.0, 20.0, -60.0, 60.0]);
    }

    public function test_tax_breakdown_without_adjustment_notes_is_unchanged(): void
    {
        $invoice = $this->createInvoice(withAdjustment: false);

        $this->assertSame(120.0, $invoice->total);

        $page = Livewire::test(Show::class, ['invoice' => $invoice])
            ->assertSeeInOrder(['Subtotal', '$100.00', 'VAT', '$20.00', 'Total', '$120.00'])
            ->assertDontSee('Adjustments')
            ->html();
        $this->assertTotalsAddUp($page, [100.0, 20.0, 120.0]);

        $pdf = view('pdf.invoice', ['invoice' => $invoice])->render();
        $this->assertStringNotContainsString('Adjustments', $pdf);
        $this->assertTotalsAddUp($pdf, [100.0, 20.0, 120.0]);
    }
}
