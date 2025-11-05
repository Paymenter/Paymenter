<?php

namespace App\Admin\Widgets;

use App\Models\InvoiceTransaction;
use App\Models\Service;
use App\Models\Ticket;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Model;

class Overview extends BaseWidget
{
    // Poll every 5 minutes (5m doesn't work somehow)
    protected ?string $pollingInterval = '600s';

    protected function getStats(): array
    {
        return [
            $this->invoiceTransaction(),
            $this->getData(Ticket::class, 'Tickets'),
            $this->getData(Service::class, 'Services'),
        ];
    }

    private function invoiceTransaction()
    {
        $chart = Trend::query(InvoiceTransaction::query()->where('status', \App\Enums\InvoiceTransactionStatus::Succeeded)->where('is_credit_transaction', false))
            ->between(
                start: now()->subMonth()->startOfDay(),
                end: now(),
            )
            ->perDay()->sum('amount');

        $thisMonth = $chart->sum('aggregate');

        $lastMonth = InvoiceTransaction::query()
            ->whereBetween('created_at', [now()->subMonths(2)->startOfDay(), now()->subMonth()->endOfDay()])
            ->sum('amount');

        $increase = $thisMonth - $lastMonth;

        $percentageIncrease = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return Stat::make('Revenue', $thisMonth)
            ->description($increase >= 0 ? 'Increased by ' . number_format($percentageIncrease, 2) . '% (last 30 days)' : 'Decreased by ' . number_format($percentageIncrease, 2) . '% (last 30 days)')
            ->descriptionIcon($increase >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->chart($chart->map(fn (TrendValue $value) => $value->aggregate)->toArray())
            ->color($increase >= 0 ? 'success' : 'danger');
    }

    private function getData($model, $name, $sum = false)
    {
        $model = $model instanceof Model ? get_class($model) : $model;

        $chart = Trend::model($model)
            ->between(
                start: now()->subMonth()->startOfDay(),
                end: now(),
            )
            ->perDay()
            ->count();

        $thisMonth = $chart->sum('aggregate');

        $lastMonth = $model::query()
            ->whereBetween('created_at', [now()->subMonths(2)->startOfDay(), now()->subMonth()->endOfDay()])
            ->count();

        $increase = $thisMonth - $lastMonth;

        $percentageIncrease = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return Stat::make($name, $thisMonth)
            ->description($increase >= 0 ? 'Increased by ' . number_format($percentageIncrease, 2) . '% (last 30 days)' : 'Decreased by ' . number_format($percentageIncrease, 2) . '% (last 30 days)')
            ->descriptionIcon($increase >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->chart($chart->map(fn (TrendValue $value) => $value->aggregate)->toArray())
            ->color($increase >= 0 ? 'success' : 'danger');
    }

    public static function canView(): bool
    {
        return auth()->user()->hasPermission('admin.widgets.overview');
    }
}
