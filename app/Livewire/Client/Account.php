<?php

namespace App\Livewire\Client;

use App\Livewire\ComponentWithProperties;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Account extends ComponentWithProperties
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public function mount()
    {
        $user = Auth::user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;

        $this->initializeProperties($user, $user::class);
    }

    public function rules()
    {
        return array_merge([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
        ], $this->getRulesForProperties());
    }

    public function validationAttributes()
    {
        return $this->getAttributesForProperties();
    }

    public function submit()
    {
        $validatedData = $this->validate();

        /** @var User $user */
        $user = Auth::user();
        $user->update($validatedData);

        if (array_key_exists('properties', $validatedData)) {
            $this->updateProperties($user, $validatedData['properties']);
        }

        $this->notify(__('Account updated successfully.'));
    }

    public function render()
    {
        return view('client.account.index')->layoutData([
            'sidebar' => true,
            'title' => 'Account',
        ]);
    }
}
