<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GoogleProvider;
use SocialiteProviders\Discord\Provider as DiscordProvider;

class SocialLoginController extends Controller
{
    protected array $providers = [
        'discord' => [
            'driver' => DiscordProvider::class,
            'scopes' => ['email'],
            'redirect' => '/oauth/discord/callback'
        ],
        'github' => [
            'driver' => GithubProvider::class,
            'scopes' => ['user:email'],
            'redirect' => '/oauth/github/callback'
        ],
        'google' => [
            'driver' => GoogleProvider::class,
            'scopes' => ['email'],
            'redirect' => '/oauth/google/callback'
        ]
    ];

    public function redirect(string $provider): RedirectResponse
    {
        if (!$this->isProviderEnabled($provider)) {
            abort(404, 'Provider not available');
        }

        $driver = $this->getDriver($provider);
        $scopes = $this->providers[$provider]['scopes'];

        return $driver->scopes($scopes)->redirect();
    }

    public function handle(string $provider): RedirectResponse
    {
        if (!$this->isProviderEnabled($provider)) {
            abort(404, 'Provider not available');
        }

        try {
            $oauthUser = $this->getDriver($provider)->user();

            // Provider-specific validation
            if (!$this->validateOAuthUser($provider, $oauthUser)) {
                return redirect()->route('login')
                    ->with('error', __('auth.oauth.invalid_account'));
            }

            DB::beginTransaction();

            $user = $this->findOrCreateUser($provider, $oauthUser);
            $this->updateUserProperties($user, $provider, $oauthUser);

            DB::commit();

            Auth::login($user, true);

            return redirect()->route('home');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("OAuth login failed for provider {$provider}: " . $e->getMessage());

            return redirect()->route('login')
                ->with('error', __('auth.oauth.login_failed'));
        }
    }

    protected function isProviderEnabled(string $provider): bool
    {
        return array_key_exists($provider, $this->providers) &&
            config("settings.oauth_{$provider}");
    }

    protected function getDriver(string $provider)
    {
        $config = $this->providers[$provider];

        return Socialite::buildProvider($config['driver'], [
            'client_id' => config("settings.oauth_{$provider}_client_id"),
            'client_secret' => config("settings.oauth_{$provider}_client_secret"),
            'redirect' => $config['redirect'],
        ]);
    }

    protected function validateOAuthUser(string $provider, $oauthUser): bool
    {
        // Check if email exists
        if (empty($oauthUser->email)) {
            return false;
        }

        // Discord-specific validation
        if ($provider === 'discord') {
            return isset($oauthUser->user['verified']) &&
                $oauthUser->user['verified'] === true;
        }

        return true;
    }

    protected function findOrCreateUser(string $provider, $oauthUser): User
    {
        $user = User::where('email', $oauthUser->email)->first();

        if ($user) {
            return $user;
        }

        $userData = $this->getUserDataFromOAuth($provider, $oauthUser);

        $user = User::create([
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'email' => $oauthUser->email,
            'email_verified_at' => now(),
            'password' => bcrypt(Str::random(32))
        ]);

        if (!$user) {
            throw new \Exception('Failed to create user account');
        }

        return $user;
    }

    protected function getUserDataFromOAuth(string $provider, $oauthUser): array
    {
        return match ($provider) {
            'discord' => [
                'first_name' => $oauthUser->name ?? null,
                'last_name' => $oauthUser->nickname ?? null,
            ],
            'google' => [
                'first_name' => $oauthUser->user['given_name'] ?? $oauthUser->name ?? null,
                'last_name' => $oauthUser->user['family_name'] ?? null,
            ],
            'github' => [
                'first_name' => $oauthUser->nickname ?? $oauthUser->name ?? null,
                'last_name' => $oauthUser->nickname ?? $oauthUser->name ?? null,
            ],
            default => [
                'first_name' => $oauthUser->name ?? null,
                'last_name' => $oauthUser->name,
            ]
        };
    }

    protected function updateUserProperties(User $user, string $provider, $oauthUser): void
    {
        $user->properties()->updateOrCreate(
            ['key' => "{$provider}_id"],
            [
                'value' => $oauthUser->id,
                'name' => ucfirst($provider) . ' ID',
            ]
        );
    }
}
