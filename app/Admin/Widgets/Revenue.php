<?php

namespace App\Admin\Widgets;

use App\Enums\DashboardPeriod;
use App\Enums\InvoiceTransactionStatus;
use App\Models\InvoiceTransaction;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class Revenue extends ChartWidget
{
    protected ?string $heading = 'Revenue';

    public ?string $filter = 'month';

    protected ?string $pollingInterval = null;

    protected function getFilters(): ?array
    {
        return DashboardPeriod::options();
    }

    /**
     * Let the overview stats follow the period chosen here.
     */
    public function updatedFilter(): void
    {
        $this->dispatch('dashboard-period-updated', period: $this->filter);
    }

    protected function getData(): array
    {
        $period = DashboardPeriod::fromValue($this->filter);

        $start = $period->start();

        $end = now();

        $interval = $period->interval();

        $revenue = Trend::query(InvoiceTransaction::query()->where('status', InvoiceTransactionStatus::Succeeded)->where('is_credit_transaction', false))
            ->between(
                start: $start,
                end: $end,
            )
            ->interval($interval)
            ->sum('amount');

        $netRevenue = Trend::query(InvoiceTransaction::query()->where('status', InvoiceTransactionStatus::Succeeded)->where('is_credit_transaction', false))
            ->between(
                start: $start,
                end: $end,
            )
            ->interval($interval)
            ->sum('amount - COALESCE(fee, 0)');

        $newOrders = Trend::model(Order::class)
            ->between(
                start: $start,
                end: $end,
            )
            ->interval($interval)
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $revenue->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'backgroundColor' => '#3490dc',
                    'borderColor' => '#3490dc',
                ],
                [
                    'label' => 'Net Revenue',
                    'data' => $netRevenue->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'backgroundColor' => '#38c172',
                    'borderColor' => '#38c172',
                ],
                [
                    'label' => 'New Orders',
                    'data' => $newOrders->map(fn (TrendValue $value) => $value->aggregate)->toArray(),
                    'backgroundColor' => '#e3342f',
                    'borderColor' => '#e3342f',
                ],
            ],
            'labels' => $revenue->map(fn (TrendValue $value) => Carbon::parse($value->date)->format($period->dateFormat()))->toArray(),
        ];
    }

    protected function getOptions(): array|RawJs|null
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    public static function canView(): bool
    {
        return auth()->user()->hasPermission('admin.widgets.revenue');
    }
}
