<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        $socialUser = Socialite::driver($provider)->user();
        $user = User::where('email', $socialUser->email)->first();

        switch ($provider) {
            case 'discord':
                if ($socialUser->user["verified"] == false) {
                    return redirect()->route('login')->with('error', 'Your Discord account is not verified.');
                }
                break;

            case 'google':
                // You can add any specific logic related to Google provider here
                break;

            case 'github':
                // You can add any specific logic related to GitHub provider here
                break;

            default:
                return redirect()->route('login');
        }

        if (!$user) {
            // Generate a random password
            $randomPassword = Str::random(12); // Adjust the length as needed

            // Hash the password
            $hashedPassword = Hash::make($randomPassword);

            // User doesn't exist, so register them
            $newUser = User::create([
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'password' => $hashedPassword, // Store the hashed password
                'api_token' => Str::random(60),
                'is_social_user' => true, // Set the flag for social registration
                // Set other fields as needed
            ]);

            // Log in the newly registered user
            Auth::login($newUser, true);

            event(new Registered($newUser));

            return redirect()->route('index');
        } else {
            // User exists, log them in
            Auth::login($user, true);

            return redirect()->route('index');
        }
    }
}
