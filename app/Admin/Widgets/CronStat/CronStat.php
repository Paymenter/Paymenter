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

        $formattedDate = Carbon::parse($date)->toFormattedDateString();

        return [
            Stat::make(__('cron.invoices_created'), \App\Models\CronStat::where('key', 'invoices_created')->where('date', $date)->sum('value'))
                ->description(__('cron.invoices_created_desc', ['date' => $formattedDate])),
            Stat::make(__('cron.services_suspended'), \App\Models\CronStat::where('key', 'services_suspended')->where('date', $date)->sum('value'))
                ->description(__('cron.services_suspended_desc', ['date' => $formattedDate])),
            Stat::make(__('cron.services_terminated'), \App\Models\CronStat::where('key', 'services_terminated')->where('date', $date)->sum('value'))
                ->description(__('cron.services_terminated_desc', ['date' => $formattedDate])),
            Stat::make(__('cron.tickets_closed'), \App\Models\CronStat::where('key', 'tickets_closed')->where('date', $date)->sum('value'))
                ->description(__('cron.tickets_closed_desc', ['date' => $formattedDate])),
            Stat::make(__('cron.invoices_charged'), \App\Models\CronStat::where('key', 'invoice_charged')->where('date', $date)->sum('value'))
                ->description(__('cron.invoices_charged_desc', ['date' => $formattedDate])),
        ];
    }
}
