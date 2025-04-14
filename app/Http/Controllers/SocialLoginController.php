<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\GoogleProvider;
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

    public function handle($provider)
    {
        if ($provider == 'discord') {
            $oauth_user = $this->discord_driver->user();

            if ($oauth_user->user['verified'] == false) {
                return redirect()->route('login')->with('error', __('auth.oauth.unverified_discord_account'));
            }

            $user = User::where('email', $oauth_user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', __('auth.oauth.account_not_registered'));
            }

            Auth::login($user, true);

            return redirect()->route('home');
        } elseif ($provider == 'google') {
            $oauth_user = $this->google_driver->user();

            $user = User::where('email', $oauth_user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', __('auth.oauth.account_not_registered'));
            }

            Auth::login($user, true);

            return redirect()->route('home');
        } elseif ($provider == 'github') {
            $oauth_user = $this->github_driver->user();

            $user = User::where('email', $oauth_user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', __('auth.oauth.account_not_registered'));
            }

            Auth::login($user, true);

            return redirect()->route('home');
        } else {
            return redirect()->route('login');
        }
    }
}
