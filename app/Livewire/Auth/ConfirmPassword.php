<?php

namespace App\Livewire\Auth;

use App\Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ConfirmPassword extends Component
{
    public string $password = '';

    public function confirm()
    {
        $this->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($this->password, auth()->user()->password)) {
            $this->addError('password', __('auth.password'));

            return;
        }

        Session::put('auth.password_confirmed_at', time());

        return $this->redirectIntended(route('account.security'));
    }

    public function render()
    {
        return view('auth.confirm-password');
    }
}
