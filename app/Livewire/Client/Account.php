<?php

namespace App\Livewire\Client;

use App\Classes\Settings;
use App\Helpers\NotificationHelper;
use App\Livewire\ComponentWithProperties;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Account extends ComponentWithProperties
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public ?string $preferred_language = null;

    public function mount()
    {
        $user = Auth::user();

        $this->first_name = $user->first_name;
        $this->last_name = $user->last_name;
        $this->email = $user->email;
        $this->preferred_language = $user->preferred_language ?: config('app.locale');

        $this->initializeProperties($user, $user::class);
    }

    public function rules()
    {
        $allowed = array_keys(Settings::getAllowedLanguageOptions());

        return array_merge([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'preferred_language' => ['required', 'string', Rule::in($allowed)],
        ], $this->getRulesForProperties());
    }

    public function validationAttributes()
    {
        return array_merge([
            'preferred_language' => __('general.input.preferred_language'),
        ], $this->getAttributesForProperties());
    }

    public function submit()
    {
        $validatedData = $this->validate();

        /** @var User $user */
        $user = Auth::user();
        $user->update($validatedData);

        // If email was changed, we should mark it as unverified and send a new verification email
        if ($user->wasChanged('email')) {
            $user->email_verified_at = null;
            $user->save();
            NotificationHelper::emailVerificationNotification($user);
        }

        if ($user->wasChanged('preferred_language')) {
            session(['locale' => $user->preferred_language]);
            app()->setLocale($user->preferred_language);
        }

        if (array_key_exists('properties', $validatedData)) {
            $this->updateProperties($user, $validatedData['properties']);
        }

        $this->notify(__('Account updated successfully.'));
    }

    public function render()
    {
        return view('client.account.index', [
            'languageOptions' => Settings::getAllowedLanguageOptions(),
        ])->layoutData([
            'sidebar' => true,
            'title' => 'Account',
        ]);
    }
}
