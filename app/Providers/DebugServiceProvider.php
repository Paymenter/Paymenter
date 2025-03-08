<?php

namespace App\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Illuminate\Support\ServiceProvider;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class DebugServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->singleton(Client::class, function () {
            dd();
            $logger = new Logger('guzzle');
            $logger->pushHandler(new StreamHandler(storage_path('logs/guzzle.log'), Logger::DEBUG));

            $stack = HandlerStack::create();

            // Log to file
            $stack->push(
                Middleware::log($logger, new MessageFormatter(MessageFormatter::DEBUG))
            );

            // Log to database
            $stack->push(Middleware::tap(function ($request, $options) {
                $requestBody = $request->getBody()->getContents();

                GuzzleLog::create([
                    'method' => $request->getMethod(),
                    'url' => (string) $request->getUri(),
                    'request_body' => $requestBody ?: null,
                ]);
            }, function ($request, $response) {
                GuzzleLog::where('url', (string) $request->getUri())
                    ->latest()
                    ->first()
                    ->update([
                        'status_code' => $response->getStatusCode(),
                        'response_body' => $response->getBody()->getContents(),
                    ]);
            }));

            return new Client(['handler' => $stack]);
        });
    }
}