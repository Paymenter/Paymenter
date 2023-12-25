<?php

namespace App\Console;

use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Str;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('p:cronjob')->dailyAt(config('settings::run_cronjob_at', '00:00'));
        $schedule->command('p:check-updates')->daily();
        $schedule->command('p:stats')->dailyAt($this->registerStatsCommand());

        Setting::updateOrCreate(['key' => 'cronjob_last_run'], ['value' => now()]);
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
        $uuid = config('settings::stats.token');
        if (!$uuid) {
            $uuid = Str::uuid();
            Setting::updateOrCreate(['key' => 'stats.token'], ['value' => $uuid]);
        }
        $time = hexdec(str_replace('-', '', substr($uuid, 27))) % 1440;
        $hour = floor($time / 60);
        $minute = $time % 60;

        return "$hour:$minute";
    }
}
