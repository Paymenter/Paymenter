<?php

namespace App\Admin\Widgets;

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

    public ?string $filter = 'week';

    protected ?string $pollingInterval = null;

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Last 24 hours',
            'week' => 'Last 7 days',
            'month' => 'Last 30 days',
            'year' => 'Last 365 days',
        ];
    }

    protected function getData(): array
    {
        $start = match ($this->filter) {
            'today' => now()->subDay()->startOfDay(),
            'week' => now()->subWeek()->startOfDay(),
            'month' => now()->subMonth()->startOfDay(),
            'year' => now()->subYear()->startOfDay(),
        };

        $end = now();

        $per = match ($this->filter) {
            'today' => 'hour',
            'week' => 'day',
            'month' => 'day',
            'year' => 'month',
        };

        $revenue = Trend::query(InvoiceTransaction::query()->where('status', InvoiceTransactionStatus::Succeeded)->where('is_credit_transaction', false))
            ->between(
                start: $start,
                end: $end,
            )
            ->{'per' . ucfirst($per)}()
            ->sum('amount');

        $netRevenue = Trend::query(InvoiceTransaction::query()->where('status', InvoiceTransactionStatus::Succeeded)->where('is_credit_transaction', false))
            ->between(
                start: $start,
                end: $end,
            )
            ->{'per' . ucfirst($per)}()
            ->sum('amount - COALESCE(fee, 0)');

        $newOrders = Trend::model(Order::class)
            ->between(
                start: $start,
                end: $end,
            )
            ->{'per' . ucfirst($per)}()
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
            'labels' => $revenue->map(fn (TrendValue $value) => $this->filter === 'today' ? Carbon::parse($value->date)->format('H:i') : Carbon::parse($value->date)->format('M d'))->toArray(),
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
