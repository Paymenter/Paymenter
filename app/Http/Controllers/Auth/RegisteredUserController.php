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
use App\Validators\ReCaptcha;

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
        $countries = \App\Classes\Constants::countries();
        (new ReCaptcha())->verify($request);
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'address' => (config('settings::requiredClientDetails_address') == 1 ? 'required|': 'nullable|') . 'string',
            'city' => (config('settings::requiredClientDetails_city') == 1 ? 'required|': 'nullable|') . 'string',
            'country' => (config('settings::requiredClientDetails_country') == 1 ? 'required|': 'nullable|') . 'string|in:' . implode(',', array_keys($countries)),
            'phone' => (config('settings::requiredClientDetails_phone') == 1 ? 'required|': 'nullable|') . 'string',
        ]);


        Auth::login($user = User::create([
            'first_name' => $request->first_name,
            'email' => $request->email,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'country' => $request->country,
            'phone' => $request->phone,
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
