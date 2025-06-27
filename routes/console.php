<?php

use App\Classes\Settings;
use App\Console\Commands\CronJob;
use App\Console\Commands\TelemetryCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CronJob::class)->description('Runs daily to send out invoices, suspend servers, etc.')->daily();

if (config('app.telemetry_enabled')) {
    $settings = Settings::getTelemetry();
    Schedule::command(TelemetryCommand::class)->description('Sends telemetry data')->dailyAt($settings['hour'], $settings['minute']);
}
