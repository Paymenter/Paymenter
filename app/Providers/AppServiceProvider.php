<?php

namespace App\Providers;

use App\Classes\Synths\PriceSynth;
use App\Helpers\ExtensionHelper;
use App\Models\EmailLog;
use App\Models\Extension;
use App\Models\OauthClient;
use App\Models\User;
use App\Support\Passport\ScopeRegistry;
use Closure;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Exception;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use League\CommonMark\Extension\Table\TableExtension;
use Livewire\Livewire;
use SocialiteProviders\Discord\Provider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Service provider for settings
        $this->app->register(SettingsProvider::class);

        UrlGenerator::macro('alternateHasCorrectSignature', function (Request $request, $absolute = true, Closure|array $ignoreQuery = []) {
            // ensure the base path is applied to absolute url
            $absoluteUrl = url($request->path()); // forceRootUrl and forceScheme will apply
            $url = $absolute ? $absoluteUrl : '/' . $request->path();

            $queryString = collect(explode('&', (string) $request->server->get('QUERY_STRING')))
                ->reject(function ($parameter) use ($ignoreQuery) {
                    $parameter = Str::before($parameter, '=');

                    if ($parameter === 'signature') {
                        return true;
                    }

                    if ($ignoreQuery instanceof Closure) {
                        return $ignoreQuery($parameter);
                    }

                    return in_array($parameter, $ignoreQuery);
                })
                ->join('&');

            $original = rtrim($url . '?' . $queryString, '?');

            $keys = call_user_func($this->keyResolver);

            $keys = is_array($keys) ? $keys : [$keys];

            foreach ($keys as $key) {
                if (
                    hash_equals(
                        hash_hmac('sha256', $original, $key),
                        (string) $request->query('signature', '')
                    )
                ) {
                    return true;
                }
            }

            return false;
        });

        UrlGenerator::macro('alternateHasValidSignature', function (Request $request, $absolute = true, array $ignoreQuery = []) {
            return \URL::alternateHasCorrectSignature($request, $absolute, $ignoreQuery)
                && \URL::signatureHasNotExpired($request);
        });

        Request::macro('hasValidSignature', function ($absolute = true, array $ignoreQuery = []) {
            return \URL::alternateHasValidSignature($this, $absolute, $ignoreQuery);
        });

        Request::macro('livewireUrl', function () {
            // Somehow people manage to have no route
            $route = request()->route();

            if ($route && $route->named('paymenter.livewire.update')) {
                $previousUrl = url()->previous();

                return $previousUrl !== null ? $previousUrl : request()->fullUrl();
            }

            return request()->fullUrl();
        });

        Request::macro('livewireRoute', function () {
            // Return name of current route
            if (request()->route()->named('paymenter.livewire.update')) {
                $previousUrl = url()->previous();

                if ($previousUrl !== null) {
                    $previousRequest = \Route::getRoutes()->match(request()->create($previousUrl));
                    if ($previousRequest) {
                        return $previousRequest->getName();
                    }
                }

                return 'paymenter.livewire.update';
            }

            return request()->route()->getName();
        });

        $this->app->singleton(Handler::class, function ($app) {
            return new \App\Exceptions\ErrorHandler($app);
        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Change livewire url
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/paymenter/update', $handle)->middleware('web')->name('paymenter.');
        });
        Livewire::propertySynthesizer(PriceSynth::class);

        Gate::define('has-permission', function (User $user, string $ability) {
            return $user->hasPermission($ability);
        });

        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('discord', Provider::class);
        });

        try {
            foreach (
                collect(Extension::where(function ($query) {
                    $query->where('enabled', true)->orWhere('type', 'server')->orWhere('type', 'gateway');
                })->get())->unique('extension') as $extension
            ) {
                ExtensionHelper::call($extension, 'boot', mayFail: true);
            }
        } catch (Exception $e) {
            // Fail silently
        }

        Queue::after(function (JobProcessed $event) {
            if ($event->job->resolveName() === 'App\Mail\Mail') {
                $payload = json_decode($event->job->getRawBody());
                $data = unserialize($payload->data->command);
                EmailLog::where('id', $data->mailable->email_log_id)->update([
                    'sent_at' => now(),
                    'status' => 'sent',
                ]);
            }
        });
        Queue::failing(function (JobFailed $event) {
            if ($event->job->resolveName() === 'App\Mail\Mail') {
                $payload = json_decode($event->job->getRawBody());
                $data = unserialize($payload->data->command);
                EmailLog::where('id', $data->mailable->email_log_id)->update([
                    'status' => 'failed',
                    'error' => $event->exception->getMessage(),
                    'job_uuid' => $event->job->uuid(),
                ]);
            }
        });

        Str::macro('markdown', function ($markdown) {
            return Str::markdown($markdown, extensions: [
                new TableExtension,
            ]);
        });
        Passport::clientModel(OauthClient::class);
        Passport::ignoreRoutes();
        Passport::tokensCan(ScopeRegistry::getAll());

        if (class_exists(Scramble::class)) {
            Scramble::configure()
                ->routes(function (\Illuminate\Routing\Route $route) {
                    return Str::startsWith($route->uri, 'api/v1/admin');
                })
                ->withDocumentTransformers(function (OpenApi $openApi) {
                    $openApi->secure(
                        SecurityScheme::http('bearer')
                    );
                });
        }
    }
}
