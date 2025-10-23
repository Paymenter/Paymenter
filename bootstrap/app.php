<?php

use App\Http\Middleware\Api\AdminApi;
use App\Http\Middleware\CheckoutParameterMiddleware;
use App\Http\Middleware\EnsureUserHasPermissions;
use App\Http\Middleware\ImpersonateMiddleware;
use App\Http\Middleware\ProxyMiddleware;
use App\Http\Middleware\SetLocale;
use App\Models\DebugLog;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\ViewErrorBag;
use Laravel\Passport\Http\Middleware\CheckForAnyScope;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(ProxyMiddleware::class);
        $middleware->alias([
            'has' => EnsureUserHasPermissions::class,
            'scope' => CheckForAnyScope::class,
            'api.admin' => AdminApi::class,
            'checkout' => CheckoutParameterMiddleware::class,
        ]);
        $middleware->web([
            SetLocale::class,
            ImpersonateMiddleware::class,
        ]);
    })
    ->withEvents(discover: [
        __DIR__ . '/../app/Extensions',
        __DIR__ . '/../app/Listeners',
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (Exception $exception) {
            try {
                if (!config('settings.debug', false)) {
                    return;
                }
                DebugLog::create([
                    'type' => 'exception',
                    'context' => [
                        'message' => $exception->getMessage(),
                        'file' => $exception->getFile(),
                        'line' => $exception->getLine(),
                        'trace' => $exception->getTraceAsString(),
                    ],
                ]);
            } catch (Exception $e) {
                // Do nothing
                throw $e;
            }
        });

        $exceptions->render(function (Throwable $e) {
            $status = $e instanceof HttpException ? $e->getStatusCode() : 500;
            $headers = $e instanceof HttpException ? $e->getHeaders() : [];

            $candidates = [
                "errors.$status",
                'errors.' . substr((string) $status, 0, 1) . 'xx',
                'errors.500',
            ];

            foreach ($candidates as $view) {
                if (view()->exists($view)) {
                    try {
                        return response()->view($view, [
                            'errors' => new ViewErrorBag,
                            'exception' => $e,
                        ], $status, $headers);
                    } catch (Exception $ex) {
                        return null;
                    }
                }
            }

            return null;
        });

    })->create();
