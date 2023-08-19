<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AffiliateUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'g-recaptcha-response' => 'recaptcha',
            'cf-turnstile-response' => 'recaptcha',
            'h-captcha-response' => 'recaptcha',
        ]);


        Auth::login($user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]));
        // Send email to user
        if (!config('settings::mail_disabled')) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Exception $e) {
            }
        }

        if ($request->cookie('affiliate')) {
            $affiliate = Affiliate::where('code', $request->cookie('affiliate'))->first();
            if ($affiliate) {
                $affiliateUser = new AffiliateUser();
                $affiliateUser->affiliate()->associate($affiliate);
                $affiliateUser->user()->associate($user);
                $affiliateUser->save();
            }
        }

        event(new Registered($user));

        return redirect(RouteServiceProvider::HOME);
    }
}
