<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    public function redirectToProvider($provider)
    {
        if ($provider == 'discord') {
            return Socialite::driver($provider)->scopes(['email'])->redirect();
        } elseif ($provider == 'github') {
            return Socialite::driver($provider)->scopes(['user:email'])->redirect();
        } elseif ($provider == 'google') {
            return Socialite::driver($provider)->scopes(['email'])->redirect();
        } else {
            return redirect()->route('login');
        }
    }

    public function handleProviderCallback($provider)
    {
        if ($provider == 'discord') {
            $user = Socialite::driver($provider)->user();
            if($user->user["verified"] == false) {
                return redirect()->route('login')->with('error', 'Your Discord account is not verified.');
            }
            $user = User::where('email', $user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', 'You are not registered on this site.');
            } else {
                Auth::login($user, true);

                return redirect()->route('index');
            }
        } elseif ($provider == 'google') {
            $user = Socialite::driver($provider)->user();
            $user = User::where('email', $user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', 'You are not registered on this site.');
            } else {
                Auth::login($user, true);

                return redirect()->route('index');
            }
        } elseif ($provider == 'github') {
            $user = Socialite::driver($provider)->user();
            $user = User::where('email', $user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', 'You are not registered on this site.');
            } else {
                Auth::login($user, true);

                return redirect()->route('index');
            }
        } else {
            return redirect()->route('login');
        }
    }
}
