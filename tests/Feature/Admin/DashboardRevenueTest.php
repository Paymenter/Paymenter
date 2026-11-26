<?php

namespace Tests\Feature\Admin;

use App\Admin\Widgets\Overview;
use App\Admin\Widgets\Revenue;
use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use App\Models\InvoiceTransaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardRevenueTest extends TestCase
{
    use RefreshDatabase;

    private function paidInvoice(float $amount, float $fee, string $transactionId): Invoice
    {
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
        $invoice->items()->create([
            'description' => 'Test Item',
            'quantity' => 1,
            'price' => $amount,
        ]);

        ExtensionHelper::addPayment($invoice->id, null, $amount, $fee, $transactionId);

        return $invoice->fresh();
    }

    private function overviewRevenueStat(): Stat
    {
        $widget = new Overview;
        $widget->period = 'month';

        return (fn () => $this->getStats())->call($widget)[0];
    }

    private function overviewRevenue(): float
    {
        return (float) $this->overviewRevenueStat()->getValue();
    }

    private function chartTotals(): array
    {
        $widget = new Revenue;
        $widget->filter = 'month';

        $datasets = (fn () => $this->getData())->call($widget)['datasets'];

        return [
            'revenue' => round(array_sum($datasets[0]['data']), 2),
            'net' => round(array_sum($datasets[1]['data']), 2),
        ];
    }

    public function test_dashboard_revenue_without_refunds(): void
    {
        $this->paidInvoice(100, 3, 'txn_full');

        $this->assertEquals(100, $this->overviewRevenue());
        $this->assertEquals(['revenue' => 100.0, 'net' => 97.0], $this->chartTotals());
    }

    public function test_dashboard_revenue_leaves_out_refunded_amounts(): void
    {
        $invoice = $this->paidInvoice(100, 3, 'txn_partial');
        ExtensionHelper::refundLocally($invoice->transactions()->first(), 40);

        $this->paidInvoice(50, 1, 'txn_refunded');
        $refunded = Invoice::latest('id')->first()->transactions()->first();
        ExtensionHelper::refundLocally($refunded, 50);

        // 60 kept from the first payment, nothing from the second; the gateway fees stay paid
        $this->assertEquals(60, $this->overviewRevenue());
        $this->assertEquals(['revenue' => 60.0, 'net' => 56.0], $this->chartTotals());
    }

    public function test_dashboard_revenue_change_leaves_out_refunds_of_the_previous_period(): void
    {
        $previous = $this->paidInvoice(100, 0, 'txn_previous')->transactions()->first();
        InvoiceTransaction::whereKey($previous->id)->update(['created_at' => now()->subDays(45)]);
        ExtensionHelper::refundLocally($previous->fresh(), 50);

        $this->paidInvoice(50, 0, 'txn_current');

        // 50 kept in each period
        $this->assertEquals(50, $this->overviewRevenue());
        $this->assertStringStartsWith('Increased by 0.00%', $this->overviewRevenueStat()->getDescription());
    }
}
