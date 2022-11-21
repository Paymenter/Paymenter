<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class SocialLoginController extends Controller
{
    public function redirectToProvider($provider)
    {
        if ($provider == 'discord') {
            return Socialite::driver($provider)->scopes(['email'])->redirect();
        } else if ($provider == 'github') {
            return Socialite::driver($provider)->scopes(['user:email'])->redirect();
        } else {
            return redirect()->route('login');
        }
    }

    public function handleProviderCallback($provider)
    {
        if ($provider == 'discord') {
            $user = Socialite::driver($provider)->user();
            $user = User::where('email', $user->email)->first();
            if (!$user) {
                return redirect()->route('register')->with('error', 'You are not registered on this site.');
            } else {
                Auth::login($user, true);
                return redirect()->route('home');
            }
        }else {
            return redirect()->route('login');
        }
    }
}
