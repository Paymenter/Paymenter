<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'p:check-updates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if there is an update available for the paymenter';

    protected $url = 'https://api.paymenter.org/version';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking For Updates...');

        if (config('app.version') === 'development') {
            $this->warn('You are using a development version.');
            $this->warn('This is not recommended for production environments, as it may contain errors or unstable features.');
            return Command::SUCCESS;
        } elseif (config('app.commit') && config('app.version') === 'beta') {
            $this->warn('You are using a beta version.');
            $this->warn('This is not recommended for production environments, as it may contain errors or unstable features.');

            try {
                $response = Http::get($this->url);

                $data = $response->json();

                $appVersion = config('app.commit');

                if (isset($data['beta']) && $data['beta'] !== $appVersion) {
                    $this->info('New update available! Commit ' . $data['beta']);
                    Setting::updateOrCreate(['key' => 'latest_version'], ['value' => $data['beta']]);
                } else {
                    $this->info('No updates available. Your app is up to date.');
                }
            } catch (\Exception $e) {
                $this->error('Error occurred while checking for updates: ' . $e->getMessage());
                return Command::FAILURE;
            }
            return Command::SUCCESS;
        }

        try {
            $response = Http::get($this->url);

            $data = $response->json();

            $appVersion = config('app.version');

            if (isset($data['stable']) && version_compare($appVersion, $data['stable'], '<')) {
                $this->info('New update available! Version v' . $data['stable']);
                Setting::updateOrCreate(['key' => 'latest_version'], ['value' => $data['stable']]);
            } else {
                $this->info('No updates available. Your app is up to date.');
            }
        } catch (\Exception $e) {
            $this->error('Error occurred while checking for updates: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
