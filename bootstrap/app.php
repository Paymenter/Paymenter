<?php

use App\Http\Middleware\EnsureUserHasPermissions;
use App\Http\Middleware\ProxyMiddleware;
use App\Http\Middleware\SetLocale;
use App\Providers\DebugServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Passport\Http\Middleware\CheckForAnyScope;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        DebugServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'has' => EnsureUserHasPermissions::class,
            'scope' => CheckForAnyScope::class,
        ]);
        $middleware->web(SetLocale::class);
        $middleware->append(ProxyMiddleware::class);
    })
    ->withEvents(discover: [
        __DIR__ . '/../app/Extensions',
        __DIR__ . '/../app/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
