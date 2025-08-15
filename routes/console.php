<?php

use App\Classes\Settings;
use App\Console\Commands\CronJob;
use App\Console\Commands\TelemetryCommand;
use App\Console\Commands\AutoRenewInvoices;
use Illuminate\Support\Facades\Schedule;

// Register custom console commands
Artisan::command('invoices:autorenew', function () {
    $this->call(\App\Console\Commands\AutoRenewInvoices::class);
});

Schedule::command(CronJob::class)->description('Runs daily to send out invoices, suspend servers, etc.')->daily();

$autorenewFrequency = (int) config('settings.cronjob_invoice_autorenew_frequency', 24);
Schedule::command(AutoRenewInvoices::class)
    ->description('Auto-renew invoices using credits')
    ->hourly()
    ->when(function () use ($autorenewFrequency) {
        // Only run at the specified frequency (e.g. every X hours)
        return now()->hour % $autorenewFrequency === 0;
    });

if (config('app.telemetry_enabled')) {
    $settings = Settings::getTelemetry();
    Schedule::command(TelemetryCommand::class)->description('Sends telemetry data')->dailyAt($settings['hour'], $settings['minute']);
}
