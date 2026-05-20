<?php

namespace App\Admin\Widgets\CronStat;

use App\Models\Setting;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CronOverview extends StatsOverviewWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        $lastRun = Setting::where('key', 'last_scheduler_run')->first()?->value;
        $lastCronRun = Setting::where('key', 'last_cron_run')->first()?->value;

        $cronTime = config('settings.cronjob_time', '00:00');
        $now = Carbon::now();
        $nextRun = $now->copy()->setTimeFromTimeString($cronTime);

        if ($nextRun->lessThanOrEqualTo($now)) {
            $nextRun->addDay();
        }

        return [
            Stat::make(__('cron.last_scheduler_run'), $lastRun ? Carbon::parse($lastRun)->diffForHumans() : __('cron.never'))
                ->extraAttributes([
                    'class' => $lastRun && Carbon::parse($lastRun)->gt(Carbon::now()->subMinutes(5)) ? 'success' : 'error',
                ]),
            Stat::make(__('cron.last_cron_run'), $lastCronRun ? Carbon::parse($lastCronRun)->diffForHumans() : __('cron.never'))
                ->extraAttributes([
                    'class' => $lastCronRun && Carbon::parse($lastCronRun)->gt(Carbon::now()->subHours(24)) ? 'success' : 'error',
                ]),
            Stat::make(__('cron.next_run'), $nextRun->diffForHumans()),
        ];
    }
}
