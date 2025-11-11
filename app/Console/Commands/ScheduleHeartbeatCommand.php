<?php

namespace App\Console\Commands;

use App\Models\CronStat;
use App\Models\Setting;
use Illuminate\Console\Command;

class ScheduleHeartbeatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schedule-heartbeat-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Heartbeat command to indicate the scheduler is running';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Setting::updateOrCreate(
            [
                'key' => 'last_scheduler_run',
                'settingable_type' => CronStat::class,
            ],
            ['value' => now()->toDateTimeString()]
        );
    }
}
