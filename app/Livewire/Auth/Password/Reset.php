<?php

namespace App\Livewire\Auth\Password;

use App\Livewire\Component;
use App\Models\User;
use App\Traits\Captchable;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;

class Reset extends Component
{
    use Captchable;

    public string $token;

    #[Url, Locked]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    #[Validate('required')]
    public string $password_confirmation = '';

    public function mount()
    {
        if (!$this->token || !Request::has('email')) {
            return abort(404);
        }
        // Validate token
        if (!Password::tokenExists(User::where('email', $this->email)->firstOrFail(), $this->token)) {
            return abort(404);
        }
    }

    public function submit()
    {
        $this->validate();

        $this->captcha();

        $status = Password::reset(
            ['email' => $this->email, 'password' => $this->password, 'password_confirmation' => $this->password_confirmation, 'token' => $this->token],
            function ($user) {
                $user->forceFill([
                    'password' => Hash::make($this->password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET ? $this->redirect(route('login')) : $this->notify($status, 'error');
    }

    public function render()
    {
        return view('auth.password.reset');
    }
}
