<?php

namespace App\Livewire\Auth;

use App\Livewire\Component;
use App\Models\User;
use App\Traits\Captchable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Validate;

class Login extends Component
{
    use Captchable;

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    public $remember = false;

    public function submit()
    {
        $this->captcha();
        $this->validate();

        if (RateLimiter::tooManyAttempts('login:' . $this->email, 5)) {
            $this->addError('email', 'Too many login attempts. Please try again in 60 seconds.');

            return;
        }

        RateLimiter::increment('login:' . $this->email);

        if (!Auth::attempt($this->only('email', 'password'), $this->remember)) {
            $this->addError('email', 'These credentials do not match our records.');

            return;
        }

        // Check 2FA
        if (Auth::user()->tfa_secret) {
            Session::put('2fa', [
                'user_id' => Auth::id(),
                'remember' => $this->remember,
                'expires' => now()->addMinutes(5),
            ]);

            Auth::logout();

            return $this->redirect(route('2fa'), true);
        }

        RateLimiter::clear('login:' . $this->email);

        event(new \App\Events\Auth\Login(User::find(Auth::id())));

        $intendedUrl = session()->pull('url.intended', default: route('dashboard'));
        $isAdminRoute = str_starts_with($intendedUrl, url('/admin'));

        // Redirect normally if it is an admin route, otherwise navigate using livewire
        return $this->redirect($intendedUrl, navigate: !$isAdminRoute);
    }

    public function render()
    {
        return view('auth.login');
    }
}
