<?php

namespace App\Admin\Widgets;

use App\Models\InvoiceTransaction;
use App\Models\Service;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class Overview extends BaseWidget
{
    // Poll every 5 minutes
    protected static ?string $pollingInterval = '5m';

    protected function getStats(): array
    {
        $now = now();

        // Calculate revenue increase
        $revenueThisMonth = InvoiceTransaction::query()
            ->whereBetween('created_at', [$now->copy()->subMonth(), $now])
            ->sum('amount');
        $revenueLastMonth = InvoiceTransaction::query()
            ->whereBetween('created_at', [$now->copy()->subMonths(2), $now->copy()->subMonth()])
            ->sum('amount');
        $revenueIncrease = $revenueThisMonth - $revenueLastMonth;

        // Calculate percentage increase
        $revenuePercentageIncrease = $revenueLastMonth > 0 ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100 : 0;

        // 10 random from the last 30 days for the chart
        $dates = [];
        // Get the data for the chart
        for ($i = 0; $i < 30; $i += 3) {
            // Where date in between $i and $i+3
            $dates[] = InvoiceTransaction::query()
                ->whereBetween('created_at', [$now->copy()->subDays($i + 3), $now->copy()->subDays($i)])
                ->sum('amount') ?? 0;
        }

        // Tickets increase
        $ticketsThisMonth = Ticket::query()
            ->whereBetween('created_at', [$now->copy()->subMonth(), $now])
            ->count();
        $ticketsLastMonth = Ticket::query()
            ->whereBetween('created_at', [$now->copy()->subMonths(2), $now->copy()->subMonth()])
            ->count();
        $ticketsIncrease = $ticketsThisMonth - $ticketsLastMonth;

        // Calculate percentage increase
        $ticketsPercentageIncrease = $ticketsLastMonth > 0 ? (($ticketsThisMonth - $ticketsLastMonth) / $ticketsLastMonth) * 100 : 0;

        // 10 random from the last 30 days for the chart
        $dates = [];
        // Get the data for the chart
        for ($i = 0; $i < 30; $i += 3) {
            // Where date in between $i and $i+3
            $dates[] = Ticket::query()
                ->whereBetween('created_at', [$now->copy()->subDays($i + 3), $now->copy()->subDays($i)])
                ->count() ?? 0;
        }

        // Services increase
        $servicesThisMonth = Service::query()
            ->whereBetween('created_at', [$now->copy()->subMonth(), $now])
            ->count();
        $servicesLastMonth = Service::query()
            ->whereBetween('created_at', [$now->copy()->subMonths(2), $now->copy()->subMonth()])
            ->count();

        $servicesIncrease = $servicesThisMonth - $servicesLastMonth;

        // Calculate percentage increase
        $servicesPercentageIncrease = $servicesLastMonth > 0 ? (($servicesThisMonth - $servicesLastMonth) / $servicesLastMonth) * 100 : 0;

        // 10 random from the last 30 days for the chart
        $dates = [];
        // Get the data for the chart
        for ($i = 0; $i < 30; $i += 3) {
            // Where date in between $i and $i+3
            $dates[] = Service::query()
                ->whereBetween('created_at', [$now->copy()->subDays($i + 3), $now->copy()->subDays($i)])
                ->count() ?? 0;
        }

        return [
            Stat::make('Revenue', number_format($revenueThisMonth, 2))
                ->description($revenueIncrease >= 0 ? 'Increased by ' . number_format($revenuePercentageIncrease, 2) . '% (this month)' : 'Decreased by ' . number_format($revenuePercentageIncrease, 2) . '% (this month)')
                ->descriptionIcon($revenueIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart(array_reverse($dates))
                ->color($revenueIncrease >= 0 ? 'success' : 'danger'),
            Stat::make('New Tickets', number_format($ticketsThisMonth, 0))
                ->description($ticketsIncrease >= 0 ? 'Increased by ' . number_format($ticketsPercentageIncrease, 2) . '% (this month)' : 'Decreased by ' . number_format($revenuePercentageIncrease, 2) . '% (this month)')
                ->descriptionIcon($ticketsIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart(array_reverse($dates))
                ->color($ticketsIncrease >= 0 ? 'success' : 'danger'),
            Stat::make('New Services', number_format($servicesThisMonth, 0))
                ->description($servicesIncrease >= 0 ? 'Increased by ' . number_format($servicesPercentageIncrease, 2) . '% (this month)' : 'Decreased by ' . number_format($revenuePercentageIncrease, 2) . '% (this month)')
                ->descriptionIcon($servicesIncrease >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart(array_reverse($dates))
                ->color($servicesIncrease >= 0 ? 'success' : 'danger'),
        ];
    }
}
