<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Validation\ValidationException;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     *
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        if (Auth::check() && Auth::user()->is_social_user) {
            // If user is a social user, bypass the password confirmation view
            $request->session()->put('auth.password_confirmed_at', time());
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return view('auth.passwords.confirm');
    }

    /**
     * Confirm the user's password.
     *
     * @return mixed
     */
    public function store(Request $request)
    {
        $request->validate([
            'g-recaptcha-response' => 'recaptcha',
            'cf-turnstile-response' => 'recaptcha',
            'h-captcha-response' => 'recaptcha',
        ]);

        // Check if the user is a social user (set this flag during social registration)
        if (Auth::check() && Auth::user()->is_social_user) {
            // Bypass password check for social users
            $request->session()->put('auth.password_confirmed_at', time());
            return redirect()->intended(RouteServiceProvider::HOME);
        } else {
            // Perform the password check for non-social users
            if (!Auth::guard('web')->validate([
                'email' => $request->user()->email,
                'password' => $request->password,
            ])) {
                throw ValidationException::withMessages(['password' => __('auth.password')]);
            }
        }

        // Update session and redirect
        $request->session()->put('auth.password_confirmed_at', time());
        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
