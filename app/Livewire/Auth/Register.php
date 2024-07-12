<?php

namespace App\Livewire\Auth;

use App\Livewire\Component;
use App\Models\User;
use App\Traits\Captchable;
use Illuminate\Support\Facades\Hash;

class Register extends Component
{
    use Captchable;

    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $phone = '';

    public string $company_name = '';

    public string $country = '';

    public string $address = '';

    public string $address2 = '';

    public string $city = '';

    public string $state = '';

    public string $zip = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount()
    {
        $this->country = config('app.countries')[config('settings.company_country')] ?? config('app.countries')['US'];
    }

    public function rules()
    {
        return [
            'first_name' => (!in_array('first_name', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'last_name' => (!in_array('last_name', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => (!in_array('phone', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'company_name' => (!in_array('company_name', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'country' => (!in_array('country', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255|in:' . implode(',', config('app.countries')),
            'address' => (!in_array('address', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'address2' => (!in_array('address2', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'city' => (!in_array('city', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'state' => (!in_array('state', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'zip' => (!in_array('zip', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function submit()
    {
        $this->validate();

        $user = User::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company_name' => $this->company_name,
            'country' => $this->country,
            'address' => $this->address,
            'address2' => $this->address2,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'password' => Hash::make($this->password),
        ]);

        auth()->login($user);

        return redirect()->intended(route('dashboard'));
    }

    public function render()
    {
        return view('auth.register');
    }
}
