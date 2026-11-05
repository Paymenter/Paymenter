<?php

namespace App\Livewire\Auth\Password;

use App\Livewire\Component;
use App\Models\User;
use App\Traits\Captchable;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Request;
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

        // Reset links expire and can only be used once, so send the user back to request a new one.
        // An unknown email gets the same answer to avoid disclosing which accounts exist.
        $user = User::where('email', $this->email)->first();
        if (!$user || !Password::tokenExists($user, $this->token)) {
            $this->notify(__('passwords.token'), 'error', true);

            return $this->redirect(route('password.request'), navigate: true);
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
                ]);

                $user->save();

                // Revoke all existing sessions
                $user->sessions()->delete();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET ? $this->redirect(route('login')) : $this->notify(__($status), 'error');
    }

    public function render()
    {
        return view('auth.password.reset');
    }
}
