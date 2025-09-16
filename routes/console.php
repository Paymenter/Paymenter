<?php

use App\Classes\Settings;
use App\Console\Commands\CronJob;
use App\Console\Commands\FetchEmails;
use App\Console\Commands\TelemetryCommand;
use Illuminate\Support\Facades\Schedule;

Schedule::command(CronJob::class)->description('Runs daily to send out invoices, suspend servers, etc.')->dailyAt(config('settings.cronjob_time', '00:00'));
Schedule::command(FetchEmails::class)->description('Import ticket emails using IMAP')->everyFiveMinutes();

if (config('app.telemetry_enabled')) {
    $settings = Settings::getTelemetry();
    Schedule::command(TelemetryCommand::class)->description('Sends telemetry data')->dailyAt($settings['hour'] . ':' . $settings['minute']);
}
