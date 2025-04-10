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
        } elseif (config('app.version') == 'beta') {
            // Check if app.commit is different from the latest commit
            $latestVersion = Http::get('https://api.github.com/repos/Paymenter/Paymenter/commits')->json();
            // Is the last commit message 'style: linted files with pint'?
            $latestVersion = collect($latestVersion)->firstWhere('commit.message', '!=', 'style: linted files with pint')['sha'];
            Setting::updateOrCreate(
                ['key' => 'latest_commit'],
                ['value' => $latestVersion]
            );
            if (config('app.commit') != $latestVersion) {
                $this->info('A new version is available: ' . $latestVersion);
                // Save as a variable to use in the UI
                $this->info('Latest version saved to database.');
            } else {
                $this->info('You are using the latest version: ' . config('app.commit'));
            }
        } else {
            // Check if app.version is different from the latest version
            $latestVersion = Http::get('https://api.github.com/repos/Paymenter/Paymenter/releases/latest')->json()['tag_name'];
            // Remove the 'v' from the version
            $latestVersion = str_replace('v', '', $latestVersion);
            Setting::updateOrCreate(
                ['key' => 'latest_version'],
                ['value' => $latestVersion]
            );
            if (config('app.version') != $latestVersion) {
                $this->info('A new version is available: ' . $latestVersion);
                // Save as a variable to use in the UI
                $this->info('Latest version saved to database.');
            } else {
                $this->info('You are using the latest version: ' . config('app.version'));
            }
        }
        $this->info('Update check completed.');
    }
}
