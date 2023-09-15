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

    protected $url = 'https://api.github.com/repos/Paymenter/Paymenter/releases/latest';

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
        } else {
            try {
                $response = Http::get($this->url);

                $data = $response->json();

                $appVersion = "v" . config('app.version');

                if (isset($data['tag_name']) && version_compare($appVersion, $data['tag_name'], '<')) {
                    $this->info('New update available! Version ' . $data['tag_name']);
                    Setting::updateOrCreate(['key' => 'latest_version'], ['value' => $data['tag_name']]);
                } else {
                    $this->info('No updates available. Your app is up to date.');
                }
            } catch (\Exception $e) {
                $this->error('Error occurred while checking for updates: ' . $e->getMessage());
                return Command::FAILURE;
            }
        }

        return Command::SUCCESS;
    }
}
