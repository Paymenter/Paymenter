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
            $this->getData(InvoiceTransaction::class, 'Revenue', 'amount'),
            $this->getData(Ticket::class, 'Tickets'),
            $this->getData(Service::class, 'Services'),
        ];
    }

    private function getData($model, $name, $sum = false)
    {
        $model = $model instanceof Model ? get_class($model) : $model;

        $chart = Trend::model($model)
            ->between(
                start: now()->subMonth(),
                end: now(),
            )
            ->perDay();

        if ($sum) {
            $chart = $chart->sum($sum);
        } else {
            $chart = $chart->count();
        }

        $thisMonth = $chart->sum('aggregate');

        $lastMonth = $model::query()
            ->whereBetween('created_at', [now()->subMonths(2), now()->subMonth()]);

        if ($sum) {
            $lastMonth = $lastMonth->sum($sum);
        } else {
            $lastMonth = $lastMonth->count();
        }

        $increase = $thisMonth - $lastMonth;

        $percentageIncrease = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return Stat::make($name, $thisMonth)
            ->description($increase >= 0 ? 'Increased by ' . number_format($percentageIncrease, 2) . '% (this month)' : 'Decreased by ' . number_format($percentageIncrease, 2) . '% (this month)')
            ->descriptionIcon($increase >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->chart($chart->map(fn (TrendValue $value) => $value->aggregate)->toArray())
            ->color($increase >= 0 ? 'success' : 'danger');
    }

    public static function canView(): bool
    {
        return auth()->user()->hasPermission('admin.widgets.overview');
    }
}
