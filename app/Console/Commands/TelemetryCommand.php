<?php

namespace App\Console\Commands;

use App\Classes\Settings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PDO;

class TelemetryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:telemetry {--simulate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends telemetry data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Send telemetry data
        $this->info('Gathering telemetry data...');

        $data = [
            'uuid' => Settings::getTelemetry()['uuid'],
            'version' => config('app.version'),
            'php_version' => phpversion(),
            'drivers' => [
                'cache' => [
                    'type' => config('cache.default'),
                ],

                'database' => [
                    'type' => config('database.default'),
                    'version' => DB::getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION),
                ],
            ],
            'database_counts' => [
                'invoices' => [
                    'count' => DB::table('invoices')->count(),
                    'paid' => DB::table('invoices')->where('status', 'paid')->count(),
                ],
                'services' => [
                    'count' => DB::table('services')->count(),
                    'active' => DB::table('services')->where('status', 'active')->count(),
                ],
                'products' => [
                    'count' => DB::table('products')->count(),
                ],
                'currencies' => [
                    'count' => DB::table('currencies')->count(),
                    'currencies' => DB::table('currencies')->pluck('code')->toArray(),
                ],
                'users' => [
                    'count' => DB::table('users')->count(),
                    'admins' => DB::table('users')->where('role_id', '!=', null)->count(),
                ],
                'extensions' => [
                    'count' => DB::table('extensions')->count(),
                    'active' => DB::table('extensions')
                        ->where('enabled', true)
                        ->orWhereIn('type', ['server', 'gateway'])
                        ->pluck('extension')->toArray(),
                ],
            ],
        ];

        if ($this->option('simulate')) {
            $this->info('Simulating telemetry data...');
            $this->line(json_encode($data, JSON_PRETTY_PRINT));

            return;
        }

        // Send telemetry data
        $this->info('Sending telemetry data...');
        $response = Http::post('https://api.paymenter.org/statistics', $data)->throw();

        if ($response->successful()) {
            $this->info('Telemetry data sent successfully.');
        } else {
            $this->error('Failed to send telemetry data: ' . $response->body());
        }
    }
}
