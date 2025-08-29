<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckForUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-for-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('app.version') == 'development') {
            $this->info('You are using the development version. No update check available.');

            return;
        }

        if (config('app.version') == 'beta') {
            $version = Http::get('https://api.paymenter.org/version?beta')->json();
            Setting::updateOrCreate(
                ['key' => 'latest_commit'],
                ['value' => $version['beta']]
            );
            if (config('app.commit') != $version['beta']) {
                $this->info('A new version is available: ' . $version['beta']);
                // Save as a variable to use in the UI
                $this->info('Latest version saved to database.');
            } else {
                $this->info('You are using the latest version: ' . config('app.commit'));
            }
        } else {
            $version = Http::get('https://api.paymenter.org/version')->json();

            if (config('app.version') != $version['latest']) {
                $this->info('A new version is available: ' . $version['latest']);
                // Save as a variable to use in the UI
                $this->info('Latest version saved to database.');
            } else {
                $this->info('You are using the latest version: ' . config('app.version'));
            }
        }
        Setting::updateOrCreate(
            ['key' => 'latest_version'],
            ['value' => $version['latest']]
        );

        $this->info('Update check completed.');
    }
}
