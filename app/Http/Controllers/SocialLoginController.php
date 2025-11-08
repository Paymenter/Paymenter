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

            // Security: Verify email is confirmed from OAuth provider
            if ($oauth_user->user['verified'] == false) {
                return redirect()->route('login')->with('error', __('auth.oauth.unverified_discord_account'));
            }

            // Security: Only allow OAuth login if email is verified
            if (!isset($oauth_user->email) || empty($oauth_user->email)) {
                return redirect()->route('login')->with('error', __('auth.oauth.email_required'));
            }

            $user = User::where('email', $oauth_user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', __('auth.oauth.account_not_registered'));
            }

            // Security: Verify the account email matches and is verified
            if (!$user->email_verified_at) {
                return redirect()->route('login')->with('error', __('auth.oauth.email_not_verified'));
            }

            Auth::login($user, true);

            return redirect()->route('home');
        } elseif ($provider == 'google') {
            $oauth_user = $this->google_driver->user();

            // Security: Only allow OAuth login if email is provided and verified
            if (!isset($oauth_user->email) || empty($oauth_user->email)) {
                return redirect()->route('login')->with('error', __('auth.oauth.email_required'));
            }

            // Google emails are verified by default, but check if available
            if (isset($oauth_user->user['email_verified']) && !$oauth_user->user['email_verified']) {
                return redirect()->route('login')->with('error', __('auth.oauth.unverified_email'));
            }

            $user = User::where('email', $oauth_user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', __('auth.oauth.account_not_registered'));
            }

            // Security: Verify the account email is verified
            if (!$user->email_verified_at) {
                return redirect()->route('login')->with('error', __('auth.oauth.email_not_verified'));
            }

            Auth::login($user, true);

            return redirect()->route('home');
        } elseif ($provider == 'github') {
            $oauth_user = $this->github_driver->user();

            // Security: Only allow OAuth login if email is provided
            if (!isset($oauth_user->email) || empty($oauth_user->email)) {
                return redirect()->route('login')->with('error', __('auth.oauth.email_required'));
            }

            $user = User::where('email', $oauth_user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', __('auth.oauth.account_not_registered'));
            }

            // Security: Verify the account email is verified
            if (!$user->email_verified_at) {
                return redirect()->route('login')->with('error', __('auth.oauth.email_not_verified'));
            }

            Auth::login($user, true);

            return redirect()->route('home');
        } else {
            return redirect()->route('login');
        }
    }
}
