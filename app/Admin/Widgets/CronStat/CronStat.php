<?php

namespace App\Admin\Widgets\CronStat;

use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CronStat extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = null;

    public function getColumns(): int|array
    {
        return 2;
    }

    protected function getStats(): array
    {
        $date = $this->pageFilters['date'] ?? now()->toDateString();

        return [
            Stat::make('Invoices Created', \App\Models\CronStat::where('key', 'invoices_created')->where('date', $date)->sum('value'))
                ->description('Total renewal invoices created on ' . Carbon::parse($date)->toFormattedDateString()),
            Stat::make('Services Suspended', \App\Models\CronStat::where('key', 'services_suspended')->where('date', $date)->sum('value'))
                ->description('Total overdue services suspended on ' . Carbon::parse($date)->toFormattedDateString()),
            Stat::make('Services Terminated', \App\Models\CronStat::where('key', 'services_terminated')->where('date', $date)->sum('value'))
                ->description('Total overdue services terminated on ' . Carbon::parse($date)->toFormattedDateString()),
            Stat::make('Tickets Closed', \App\Models\CronStat::where('key', 'tickets_closed')->where('date', $date)->sum('value'))
                ->description('Total inactive tickets closed on ' . Carbon::parse($date)->toFormattedDateString()),
            Stat::make('Invoices Charged', \App\Models\CronStat::where('key', 'invoice_charged')->where('date', $date)->sum('value'))
                ->description('Total invoices charged on ' . Carbon::parse($date)->toFormattedDateString()),
        ];
    }
}
