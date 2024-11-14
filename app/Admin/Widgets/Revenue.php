<?php

namespace App\Admin\Widgets;

use App\Models\InvoiceTransaction;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class Revenue extends ChartWidget
{
    protected static ?string $heading = 'Revenue';

    public ?string $filter = 'week';

    protected static ?string $pollingInterval = null;

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getData(): array
    {
        $transactions = InvoiceTransaction::query()
            ->whereBetween('created_at', $this->getDateRange())
            ->get();

        $last24Hours = collect(range(0, 23))->map(fn ($hour) => now()->subHours($hour)->format('H'))->reverse()->values();
        $last7Days = collect(range(0, 6))->map(fn ($day) => now()->subDays($day)->format('D'))->reverse()->values();
        $last30Days = collect(range(0, 29))->map(fn ($day) => now()->subDays($day)->format('d'))->reverse()->values();
        $last12Months = collect(range(0, 11))->map(fn ($month) => now()->subMonths($month)->format('M'))->reverse()->values();

        $labels = match ($this->filter) {
            'today' => $last24Hours,
            'week' => $last7Days,
            'month' => $last30Days,
            'year' => $last12Months,
        };

        // If today add data for every hour
        $revenue = match ($this->filter) {
            'today' => collect($labels)->mapWithKeys(fn ($hour) => [$hour => 0])->merge(
                $transactions->groupBy(fn ($transaction) => $transaction->created_at->format('H'))
                    ->map(fn ($transactions) => $transactions->sum('amount'))
            )->values(),
            'week' => collect($labels)->mapWithKeys(fn ($day) => [$day => 0])->merge(
                $transactions->groupBy(fn ($transaction) => $transaction->created_at->format('D'))
                    ->map(fn ($transactions) => $transactions->sum('amount'))
            )->values(),
            'month' => collect($labels)->mapWithKeys(fn ($day) => [$day => 0])->merge(
                $transactions->groupBy(fn ($transaction) => $transaction->created_at->format('d'))
                    ->map(fn ($transactions) => $transactions->sum('amount'))
            )->values(),
            'year' => collect($labels)->mapWithKeys(fn ($month) => [$month => 0])->merge(
                $transactions->groupBy(fn ($transaction) => $transaction->created_at->format('M'))
                    ->map(fn ($transactions) => $transactions->sum('amount'))
            )->values(),
        };

        $netRevenue = match ($this->filter) {
            'today' => collect($labels)->mapWithKeys(fn ($hour) => [$hour => 0])->merge(
                $transactions->groupBy(fn ($transaction) => $transaction->created_at->format('H'))
                    ->map(fn ($transactions) => $transactions->sum('amount') - $transactions->sum('fee'))
            )->values(),
            'week' => collect($labels)->mapWithKeys(fn ($day) => [$day => 0])->merge(
                $transactions->groupBy(fn ($transaction) => $transaction->created_at->format('D'))
                    ->map(fn ($transactions) => $transactions->sum('amount') - $transactions->sum('fee'))
            )->values(),
            'month' => collect($labels)->mapWithKeys(fn ($day) => [$day => 0])->merge(
                $transactions->groupBy(fn ($transaction) => $transaction->created_at->format('d'))
                    ->map(fn ($transactions) => $transactions->sum('amount') - $transactions->sum('fee'))
            )->values(),
            'year' => collect($labels)->mapWithKeys(fn ($month) => [$month => 0])->merge(
                $transactions->groupBy(fn ($transaction) => $transaction->created_at->format('M'))
                    ->map(fn ($transactions) => $transactions->sum('amount') - $transactions->sum('fee'))
            )->values(),
        };

        $newOrders = Order::query()
            ->whereBetween('created_at', $this->getDateRange())
            ->get();

        $newOrders = match ($this->filter) {
            'today' => collect($labels)->mapWithKeys(fn ($hour) => [$hour => 0])->merge(
                $newOrders->groupBy(fn ($order) => $order->created_at->format('H'))
                    ->map(fn ($orders) => $orders->count())
            )->values(),
            'week' => collect($labels)->mapWithKeys(fn ($day) => [$day => 0])->merge(
                $newOrders->groupBy(fn ($order) => $order->created_at->format('D'))
                    ->map(fn ($orders) => $orders->count())
            )->values(),
            'month' => collect($labels)->mapWithKeys(fn ($day) => [$day => 0])->merge(
                $newOrders->groupBy(fn ($order) => $order->created_at->format('d'))
                    ->map(fn ($orders) => $orders->count())
            )->values(),
            'year' => collect($labels)->mapWithKeys(fn ($month) => [$month => 0])->merge(
                $newOrders->groupBy(fn ($order) => $order->created_at->format('M'))
                    ->map(fn ($orders) => $orders->count())
            )->values(),
        };

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $revenue,
                    'backgroundColor' => '#3490dc',
                    'borderColor' => '#3490dc',
                ],
                [
                    'label' => 'Net Revenue',
                    'data' => $netRevenue,
                    'backgroundColor' => '#38c172',
                    'borderColor' => '#38c172',
                ],
                [
                    'label' => 'New Orders',
                    'data' => $newOrders,
                    'backgroundColor' => '#e3342f',
                    'borderColor' => '#e3342f',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getDateRange(): array
    {
        $now = now();

        switch ($this->filter) {
            case 'today':
                return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
            case 'week':
                $startOfWeek = $now->copy()->subWeek();
                $endOfWeek = $now->copy();

                return [$startOfWeek, $endOfWeek];
            case 'month':
                $startOfMonth = $now->copy()->startOfMonth();
                $endOfMonth = $now->copy()->endOfMonth();

                return [$startOfMonth, $endOfMonth];
            case 'year':
                return [$now->copy()->startOfYear(), $now->copy()->endOfYear()];
        }

        return [$now->copy()->startOfDay(), $now->copy()->endOfDay()];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
