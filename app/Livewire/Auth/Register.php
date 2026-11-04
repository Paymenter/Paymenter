<?php

namespace App\Livewire\Auth;

use App\Actions\Auth\Login;
use App\Attributes\DisabledIf;
use App\Livewire\ComponentWithProperties;
use App\Models\SocialAccount;
use App\Models\User;
use App\Traits\Captchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;
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

    #[Locked]
    public bool $socialRegistration = false;

    #[Locked]
    public string $socialProvider = '';

    public function mount()
    {
        $this->initializeProperties(null, User::class);

        if ($socialRegistration = $this->pendingSocialRegistration()) {
            $this->socialRegistration = true;
            $this->socialProvider = $socialRegistration['provider'];
            $this->email = $socialRegistration['email'];
        }
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

        $socialRegistration = $this->pendingSocialRegistration();
        if ($socialRegistration) {
            // The e-mail address comes from the just-completed provider flow;
            // never trust a client-side update to the prefilled Livewire field.
            $this->email = $socialRegistration['email'];
        }

        $validatedData = $this->validate();

        $user = DB::transaction(function () use ($socialRegistration) {
            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'email_verified_at' => $socialRegistration ? now() : null,
            ]);

            if ($socialRegistration) {
                SocialAccount::create([
                    'user_id' => $user->id,
                    'provider' => $socialRegistration['provider'],
                    'provider_user_id' => $socialRegistration['provider_user_id'],
                    'linked_at' => now(),
                ]);
            }

            return $user;
        });

        if ($socialRegistration) {
            Session::forget('social_login.registration');
        }

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

    private function pendingSocialRegistration(): ?array
    {
        $registration = Session::get('social_login.registration');

        if (!is_array($registration)
            || !in_array($registration['provider'] ?? null, ['discord', 'github', 'google'], true)
            || !config('settings.oauth_' . ($registration['provider'] ?? ''))
            || !is_string($registration['provider_user_id'] ?? null)
            || !filter_var($registration['email'] ?? null, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return $registration;
    }
}
