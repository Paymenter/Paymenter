<?php

namespace App\Livewire\Clients;

use App\Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;

class Account extends Component
{
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

    public function mount()
    {
        $this->first_name = Auth::user()->first_name;
        $this->last_name = Auth::user()->last_name;
        $this->email = Auth::user()->email;
        $this->phone = Auth::user()->phone;
        $this->company_name = Auth::user()->company_name;
        $this->country = Auth::user()->country ?? config('app.countries')['US'];
        $this->address = Auth::user()->address;
        $this->address2 = Auth::user()->address2;
        $this->city = Auth::user()->city;
        $this->state = Auth::user()->state;
        $this->zip = Auth::user()->zip;
    }

    public function rules()
    {
        return [
            'first_name' => (!in_array('first_name', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'last_name' => (!in_array('last_name', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => (!in_array('phone', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'company_name' => (!in_array('company_name', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'country' => (!in_array('country', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255|in:' . implode(',', config('app.countries')),
            'address' => (!in_array('address', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'address2' => (!in_array('address2', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'city' => (!in_array('city', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'state' => (!in_array('state', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
            'zip' => (!in_array('zip', config('settings.optional_fields')) ? 'required|' : 'nullable|') . 'string|max:255',
        ];
    }

    public function submit()
    {
        $validatedData = $this->validate();
        // Don't update locked fields
        $data = collect($validatedData)->filter(function ($value, $key) {
            return !in_array($key, config('settings.locked_fields'));
        })->toArray();

        Auth::user()->update($data);

        $this->notify(__('Account updated successfully.'));
    }

    public function render()
    {
        return view('client.account.index');
    }
}
