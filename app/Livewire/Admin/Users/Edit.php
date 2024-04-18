<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    public User $user;

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
    public bool $email_verified = false;
    public ?string $email_verified_at = null;

    public function mount()
    {
        $this->first_name = $this->user->first_name;
        $this->last_name = $this->user->last_name;
        $this->email = $this->user->email;
        $this->phone = $this->user->phone;
        $this->company_name = $this->user->company_name;
        $this->country = $this->user->country;
        $this->address = $this->user->address;
        // $this->address2 = $this->user->address2;
        $this->city = $this->user->city;
        $this->state = $this->user->state;
        $this->zip = $this->user->zip;
        $this->email_verified = $this->user->hasVerifiedEmail();
        $this->email_verified_at = $this->user->email_verified_at;
    }

    public function rules()
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->user->id,
            'phone' => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255|in:' . implode(',', config('app.countries')),
            'address' => 'nullable|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ];
    }


    public function save()
    {
        $this->validate();

        $this->user->update([
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
        ]);

        if ($this->email_verified && !$this->user->hasVerifiedEmail()) {
            $this->user->markEmailAsVerified();
        } elseif (!$this->email_verified && $this->user->hasVerifiedEmail()) {
            $this->user->email_verified_at = null;
            $this->user->save();
        }

        if ($this->password) {
            $this->user->update([
                'password' => Hash::make($this->password),
            ]);
        }

        $this->dispatch('notify', __('User updated.'));
    }


    public function resendVerificationEmail()
    {
        $this->user->sendEmailVerificationNotification();
        $this->emit('notify', __('Verification email sent.'));
    }

    public function render()
    {
        return view('admin.users.edit')->layoutData(['title' => __('User: :name', ['name' => $this->user->name, 'id' => $this->user->id])]);
    }
}
