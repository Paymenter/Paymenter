<?php

namespace App\Http\Controllers;

use App\Actions\Auth\Login;
use App\Models\User;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\InvalidStateException;
use SocialiteProviders\Discord\Provider as DiscordProvider;

class SocialLoginController extends Controller
{
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
        if (!config("settings.oauth_$provider")) {
            abort(404);
        }

        return match ($provider) {
            'discord' => $this->discord_driver->scopes(['email'])->redirect(),
            'github' => $this->github_driver->scopes(['user:email'])->redirect(),
            'google' => $this->google_driver->scopes(['email'])->redirect(),
            default => abort(404)
        };
    }

    public function handle(Request $request, $provider)
    {
        $driver = match ($provider) {
            'discord' => $this->discord_driver,
            'github' => $this->github_driver,
            'google' => $this->google_driver,
            default => null,
        };

        if (!$driver) {
            return redirect()->route('login');
        }

        // Cancelling on the provider's consent screen returns ?error=access_denied without a code
        if ($request->filled('error')) {
            return $this->failed(__('auth.oauth.cancelled'));
        }

        try {
            $oauth_user = $driver->user();
        } catch (InvalidStateException) {
            // The state is gone from the session: the callback was reloaded, another
            // sign-in tab replaced it, or the session expired on the consent screen.
            return $this->failed(__('auth.oauth.expired'));
        } catch (GuzzleException $e) {
            // Wrong client credentials fail every sign-in here, so report it
            report($e);

            return $this->failed(__('auth.oauth.failed'));
        }

        if ($provider == 'discord' && !($oauth_user->user['verified'] ?? false)) {
            return $this->failed(__('auth.oauth.unverified_discord_account'));
        }

        if ($provider == 'google' && !($oauth_user->user['email_verified'] ?? false)) {
            return $this->failed(__('auth.oauth.unverified_google_account'));
        }

        // GitHub returns no email when the account has no verified primary address
        if (!$oauth_user->email) {
            return $this->failed(__('auth.oauth.no_email'));
        }

        return $this->findUserAndLogin($oauth_user->email);
    }

    private function findUserAndLogin(string $email): RedirectResponse
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->failed(__('auth.oauth.account_not_registered'), 'register');
        }

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

    private function failed(string $message, string $route = 'login'): RedirectResponse
    {
        return redirect()->route($route)->with('notification', ['message' => $message, 'type' => 'error']);
    }
}
