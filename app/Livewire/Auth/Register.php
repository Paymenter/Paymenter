<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\Login;
use App\Attributes\DisabledIf;
use App\Livewire\ComponentWithProperties;
use App\Models\User;
use App\Traits\Captchable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

#[DisabledIf('registration_disabled')]
class Register extends ComponentWithProperties
{
    use Captchable;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $tos = false;

    public function mount()
    {
        $this->initializeProperties(null, User::class);
    }

    public function rules()
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ];

        if (config('settings.tos')) {
            $rules['tos'] = 'accepted';
        }

        return array_merge($rules, $this->getRulesForProperties());
    }

    public function submit(Login $login)
    {
        $this->captcha();

        $validatedData = $this->validate();

        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if (array_key_exists('properties', $validatedData)) {
            $this->updateProperties($user, $validatedData['properties']);
        }

        $login->execute($user);

        return $this->redirectIntended(route('dashboard'), true);
    }

    public function render()
    {
        return view('auth.register');
    }
}
