<?php

namespace App\Console;

use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('p:cronjob')->everyMinute();
        $this->registerStatsCommand();
        $schedule->command('p:stats')->daily()->at(config('settings::stats.runAt'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
    }

    protected function registerStatsCommand()
    {
        if (!config('settings::stats.runAt')) {
            Setting::updateOrCreate(['key' => 'stats.runAt'], ['value' => rand(0, 23) . ':' . rand(0, 59)]);
        }
    }
}
