<?php

namespace App\Livewire\Client;

use App\Helpers\NotificationHelper;
use App\Livewire\ComponentWithProperties;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class Account extends ComponentWithProperties
{
    public string $first_name = '';

    public string $last_name = '';

    public string $email = '';

    public string $current_password = '';

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

        // The email address is where password resets go, so changing it
        // requires the current password.
        if ($validatedData['email'] !== $user->email) {
            $passwordKey = 'password-confirm:' . $user->id;

            if (RateLimiter::tooManyAttempts($passwordKey, 5)) {
                $this->addError('current_password', __('auth.throttle', [
                    'seconds' => RateLimiter::availableIn($passwordKey),
                ]));

                return;
            }

            if (!Hash::check($this->current_password, $user->password)) {
                RateLimiter::hit($passwordKey, 900);
                $this->addError('current_password', __('account.notifications.password_incorrect'));

                return;
            }

            RateLimiter::clear($passwordKey);

            // Every change sends a verification mail to the new address.
            $changeKey = 'email-change:' . $user->id;

            if (RateLimiter::tooManyAttempts($changeKey, 3)) {
                $this->addError('email', __('account.notifications.email_change_throttled', [
                    'seconds' => RateLimiter::availableIn($changeKey),
                ]));

                return;
            }

            RateLimiter::hit($changeKey, 3600);
        }

        $user->update($validatedData);

        // If email was changed, we should mark it as unverified and send a new verification email
        if ($user->wasChanged('email')) {
            $user->email_verified_at = null;
            $user->save();
            NotificationHelper::emailVerificationNotification($user);
        }

        if (array_key_exists('properties', $validatedData)) {
            $this->updateProperties($user, $validatedData['properties']);
        }

        $this->reset('current_password');

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
