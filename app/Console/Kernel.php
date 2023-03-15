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
        // $schedule->command('inspire')->hourly();
        $schedule->command('CronJob:run')->everyMinute();
        $this->registerStatsCommand();
        $schedule->command('stats:run')->daily()->at(config('settings::stats.runAt'));

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    protected function registerStatsCommand()
    {
        if(!config('settings::stats.runAt')) {
            Setting::updateOrCreate(['key' => 'stats.runAt'], ['value' => rand(0, 23) . ':' . rand(0, 59)]);
        }
        error_log('Stats will run at ' . config('settings::stats.runAt'));
    }
}
