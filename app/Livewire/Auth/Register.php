<?php

namespace App\Livewire\Auth;

use App\Livewire\ComponentWithProperties;
use App\Models\User;
use App\Traits\Captchable;
use Illuminate\Support\Facades\Hash;

class Register extends ComponentWithProperties
{
    use Captchable;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount()
    {
        $this->initializeProperties(null, User::class);
    }

    public function rules()
    {
        return array_merge([
            'first_name' => (!in_array('first_name', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'last_name' => (!in_array('last_name', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], $this->getRulesForProperties());
    }

    public function submit()
    {
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

        auth()->login($user);

        return redirect()->intended(route('dashboard'));
    }

    public function render()
    {
        return view('auth.register');
    }
}
