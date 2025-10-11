<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Logs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:logs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Post application logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Read the log file of today
        $today = now()->format('Y-m-d');
        $logFile = storage_path("logs/laravel-{$today}.log");
        if (!file_exists($logFile)) {
            $this->info('No log file found for today.');

            return;
        }

        $logContents = file_get_contents($logFile);
        // Find last error message
        $lastErrorMessage = $this->getLastErrorMessage($logContents);

        if (!$lastErrorMessage) {
            return $this->info('No error message found.');
        }

        if (!$this->confirm('Do you want to upload the error log (including environment variables) to Paymenter Support?', true)) {
            $this->info('Here is the last error message:');
            // output the last error message
            $this->line($lastErrorMessage);

            return;
        }

        // Add paymenter version and php version as first lines to $lastErrorMessage
        $paymenterVersion = config('app.version');
        $phpVersion = phpversion();
        $lastErrorMessage = "Paymenter Version: $paymenterVersion\nPHP Version: $phpVersion\nURL: " . url('/') . "\n\n$lastErrorMessage";

        // Post the error message to Paymenter Support
        // nc log.paymenter.org 99
        $this->info('Found error, uploading to Paymenter Support...');
        // Create a socket connection
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($socket, 'log.paymenter.org', 99);
        socket_write($socket, $lastErrorMessage, strlen($lastErrorMessage));

        // Read response from the server (up to 1024 bytes)
        $response = socket_read($socket, 1024);
        socket_close($socket);

        $this->line(trim($response));
    }

    protected function getLastErrorMessage(string $logContents): ?string
    {
        // [2025-07-03 11:06:06] local.ERROR: syntax error, unexpected token "if" {"exception":"[object] (ParseError(code: 0): syntax error, unexpected token \"if\" at C:\\Users\\corwi\\Projects\\Paymenter\\Paymenter\\app\\Listeners\\InvoiceItemCreatedListener.php:18)
        $pattern = '/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\].*?\.ERROR:(.*?)(?=\n\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|$)/s';
        preg_match_all($pattern, $logContents, $matches);

        if (!empty($matches[1])) {
            // Return the last error message trimmed
            return trim(end($matches[0]));
        }

        return null;
    }
}
