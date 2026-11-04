<?php

namespace App\Http\Controllers;

use App\Actions\Auth\Login;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GoogleProvider;
use SocialiteProviders\Discord\Provider as DiscordProvider;

class SocialLoginController extends Controller
{
    private const PROVIDERS = ['discord', 'github', 'google'];

    protected $discord_driver;

    protected $github_driver;

    protected $google_driver;

    public function __construct()
    {
        $this->discord_driver = Socialite::buildProvider(DiscordProvider::class, [
            'client_id' => config('settings.oauth_discord_client_id'),
            'client_secret' => config('settings.oauth_discord_client_secret'),
            'redirect' => '/oauth/discord/callback',
        ]);

        $this->github_driver = Socialite::buildProvider(GithubProvider::class, [
            'client_id' => config('settings.oauth_github_client_id'),
            'client_secret' => config('settings.oauth_github_client_secret'),
            'redirect' => '/oauth/github/callback',
        ]);

        $this->google_driver = Socialite::buildProvider(GoogleProvider::class, [
            'client_id' => config('settings.oauth_google_client_id'),
            'client_secret' => config('settings.oauth_google_client_secret'),
            'redirect' => '/oauth/google/callback',
        ]);
    }

    public function redirect($provider)
    {
        $this->ensureProviderIsEnabled($provider);

        Session::put('social_login.flow', [
            'action' => 'login',
            'provider' => $provider,
        ]);

        return $this->redirectToProvider($provider);
    }

    /** Start an explicit account-connection flow for an authenticated user. */
    public function connect(string $provider)
    {
        $this->ensureProviderIsEnabled($provider);

        Session::put('social_login.flow', [
            'action' => 'connect',
            'provider' => $provider,
            'user_id' => Auth::id(),
        ]);

        return $this->redirectToProvider($provider);
    }

    private function redirectToProvider(string $provider)
    {

        return match ($provider) {
            'discord' => $this->discord_driver->scopes(['email'])->redirect(),
            'github' => $this->github_driver->scopes(['user:email'])->redirect(),
            'google' => $this->google_driver->scopes(['email'])->redirect(),
            default => abort(404)
        };
    }

    public function handle($provider)
    {
        $this->ensureProviderIsEnabled($provider);

        $flow = Session::pull('social_login.flow');
        if (!$flow || $flow['provider'] !== $provider) {
            return $this->redirectToLoginWithError('invalid_oauth_flow');
        }

        $oauthUser = $this->getProviderUser($provider);
        $providerUserId = (string) $oauthUser->getId();
        $email = (string) $oauthUser->getEmail();

        if (!$providerUserId || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$this->emailIsVerified($provider, $oauthUser)) {
            return $this->redirectToLoginWithError('unverified_account');
        }

        if ($flow['action'] === 'connect') {
            if (!Auth::check() || Auth::id() !== $flow['user_id']) {
                return $this->redirectToLoginWithError('invalid_oauth_flow');
            }

            return $this->connectAccount(Auth::user(), $provider, $providerUserId);
        }

        $account = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->first();

        if ($account) {
            $account->update(['last_login_at' => now()]);

            return $this->login($account->user);
        }

        // Do not silently bind a new provider account to an existing local
        // account merely because both currently use the same email address.
        if (User::where('email', $email)->exists()) {
            return $this->redirectToLoginWithError('account_requires_connection');
        }

        if (config('settings.registration_disabled', false)) {
            return $this->redirectToLoginWithError('registration_disabled');
        }

        Session::put('social_login.registration', [
            'provider' => $provider,
            'provider_user_id' => $providerUserId,
            'email' => $email,
        ]);

        return redirect()->route('register');
    }

    private function login(User $user): RedirectResponse
    {
        if ($user->tfa_secret) {
            Session::put('2fa', [
                'user_id' => $user->id,
                'remember' => true,
                'expires' => now()->addMinutes(5),
            ]);

            return redirect()->route('2fa');
        }

        (new Login)->execute($user, true);

        return redirect()->route('home');
    }

    private function connectAccount(User $user, string $provider, string $providerUserId): RedirectResponse
    {
        $connectedAccount = SocialAccount::where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->first();

        if ($connectedAccount && $connectedAccount->user_id !== $user->id) {
            return $this->redirectToSecurityWithNotification('social_account_in_use', 'error');
        }

        if ($user->socialAccounts()->where('provider', $provider)->exists()) {
            return $this->redirectToSecurityWithNotification('social_account_already_connected', 'error');
        }

        $user->socialAccounts()->create([
            'provider' => $provider,
            'provider_user_id' => $providerUserId,
            'linked_at' => now(),
        ]);

        return $this->redirectToSecurityWithNotification('social_account_connected');
    }

    private function getProviderUser(string $provider)
    {
        return match ($provider) {
            'discord' => $this->discord_driver->user(),
            'github' => $this->github_driver->user(),
            'google' => $this->google_driver->user(),
        };
    }

    private function emailIsVerified(string $provider, $oauthUser): bool
    {
        return match ($provider) {
            'discord' => (bool) data_get($oauthUser->user, 'verified'),
            'google' => (bool) data_get($oauthUser->user, 'email_verified'),
            // GitHub does not expose a verification flag through Socialite's
            // user payload. Its OAuth email scope remains the provider check.
            'github' => true,
        };
    }

    private function ensureProviderIsEnabled(string $provider): void
    {
        abort_unless(in_array($provider, self::PROVIDERS, true) && config("settings.oauth_$provider"), 404);
    }

    private function redirectToLoginWithError(string $message): RedirectResponse
    {
        Session::put('notification', ['message' => __('auth.oauth.' . $message), 'type' => 'error']);

        return redirect()->route('login');
    }

    private function redirectToSecurityWithNotification(string $message, string $type = 'success'): RedirectResponse
    {
        Session::put('notification', ['message' => __('account.notifications.' . $message), 'type' => $type]);

        return redirect()->route('account.security');
    }
}
