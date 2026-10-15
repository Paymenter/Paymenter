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
            $this->addError('email', __('passwords.throttled'));

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

        $this->notify(__('passwords.sent_if_exists'), 'success');
    }

    public function render()
    {
        return view('auth.password.request');
    }
}
