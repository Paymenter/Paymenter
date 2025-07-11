<?php

namespace App\Livewire\Auth\Password;

use App\Helpers\NotificationHelper;
use App\Livewire\Component;
use App\Models\User;
use App\Traits\Captchable;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;

class Request extends Component
{
    use Captchable;

    public string $email = '';

    public function submit()
    {
        $this->captcha();

        $this->validate([
            'email' => 'required|email',
        ]);

        $rateLimitKey = 'password-reset:' . request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $this->addError('email', 'Too many password reset attempts. Please try again later.');

            return;
        }

        RateLimiter::hit($rateLimitKey, 60);

        // Find the user
        $user = User::where('email', $this->email)->first();

        if ($user && !$user?->role) {
            NotificationHelper::passwordResetNotification($user, ['url' => url(route('password.reset', [
                'token' => Password::createToken($user),
                'email' => $user->email,
            ], false))]);
        }

        $this->notify('If the email address is associated with an account, you will receive an email with instructions on how to reset your password.', 'success');
    }

    public function render()
    {
        return view('auth.password.request');
    }
}
