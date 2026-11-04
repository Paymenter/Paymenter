<?php

namespace App\Livewire\Client;

use App\Livewire\Component;
use App\Models\UserSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Locked;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\TwoFactorAuth;

class Security extends Component
{
    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $twoFactorEnabled = false;

    #[Locked]
    public $twoFactorData = [];

    #[Locked]
    public $showEnableTwoFactor = false;

    public string $twoFactorCode = '';

    #[Locked]
    public string $disconnectingProvider = '';

    #[Locked]
    public bool $showDisconnectSocialAccount = false;

    public string $disconnectPassword = '';

    public function mount()
    {
        $this->twoFactorEnabled = Auth::user()->tfa_secret ? true : false;
    }

    public function rules()
    {
        return [
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function changePassword()
    {
        $this->validate();

        if (!Hash::check($this->current_password, Auth::user()->password)) {
            return $this->notify(__('account.notifications.password_incorrect'), 'error');
        }

        Auth::user()->update([
            'password' => Hash::make($this->password),
        ]);

        // Destroy all other sessions
        Auth::user()->sessions()->where('ulid', '!=', Session::get('user_session'))->delete();

        $this->notify(__('account.notifications.password_changed'));

        $this->reset('current_password', 'password', 'password_confirmation');
    }

    public function enableTwoFactor()
    {
        if ($this->showEnableTwoFactor) {
            $this->validate([
                'twoFactorCode' => 'required|string',
            ]);

            $tfa = new TwoFactorAuth(new EndroidQrCodeProvider, config('app.name'));
            if ($tfa->verifyCode($this->twoFactorData['secret'], $this->twoFactorCode)) {
                Auth::user()->update([
                    'tfa_secret' => $this->twoFactorData['secret'],
                ]);

                $this->notify(__('account.notifications.two_factor_enabled'));

                $this->twoFactorEnabled = true;
                $this->showEnableTwoFactor = false;

                // Destroy all other sessions
                Auth::user()->sessions()->where('ulid', '!=', Session::get('user_session'))->delete();
            } else {
                $this->notify(__('account.notifications.two_factor_code_incorrect'), 'error');
            }
        } else {
            $tfa = new TwoFactorAuth(new EndroidQrCodeProvider, config('app.name'));
            $secret = $tfa->createSecret();
            $this->twoFactorData = [
                'secret' => $secret,
                'image' => $tfa->getQRCodeImageAsDataUri(Auth::user()->email, $secret),
            ];

            $this->showEnableTwoFactor = true;
        }
    }

    public function disableTwoFactor()
    {
        Auth::user()->update([
            'tfa_secret' => null,
        ]);

        $this->notify(__('account.notifications.two_factor_disabled'));

        $this->twoFactorEnabled = false;
    }

    public function logoutSession(UserSession $session)
    {
        if ($session->user_id !== Auth::id()) {
            $this->notify(__('Unauthorized'), 'error');

            return;
        }

        $session->delete();

        $this->notify(__('account.notifications.session_logged_out'));
    }

    public function startDisconnectSocialAccount(string $provider): void
    {
        if (!in_array($provider, ['discord', 'github', 'google'], true) || !Auth::user()->socialAccounts()->where('provider', $provider)->exists()) {
            $this->notify(__('account.notifications.social_account_not_connected'), 'error');

            return;
        }

        $this->disconnectingProvider = $provider;
        $this->disconnectPassword = '';
        $this->showDisconnectSocialAccount = true;
    }

    public function disconnectSocialAccount(): void
    {
        $this->validate([
            'disconnectPassword' => 'required|string',
        ]);

        $user = Auth::user();
        if (!Hash::check($this->disconnectPassword, $user->password)) {
            $this->addError('disconnectPassword', __('account.notifications.password_incorrect'));

            return;
        }

        $account = $user->socialAccounts()
            ->where('provider', $this->disconnectingProvider)
            ->first();

        if (!$account) {
            $this->notify(__('account.notifications.social_account_not_connected'), 'error');
        } else {
            $account->delete();
            $this->notify(__('account.notifications.social_account_disconnected'));
        }

        $this->reset('disconnectingProvider', 'disconnectPassword', 'showDisconnectSocialAccount');
    }

    public function socialProviders()
    {
        $accounts = Auth::user()->socialAccounts()->get()->keyBy('provider');

        return collect(['discord', 'github', 'google'])
            // A connected account remains visible even when its provider has
            // been disabled, so the user can always remove the connection.
            ->filter(fn (string $provider) => config('settings.oauth_' . $provider) || $accounts->has($provider))
            ->mapWithKeys(function (string $provider) use ($accounts) {
                return [$provider => [
                    'name' => ucfirst($provider),
                    'enabled' => (bool) config('settings.oauth_' . $provider),
                    'account' => $accounts->get($provider),
                ]];
            });
    }

    public function render()
    {
        return view('client.account.security', [
            'socialProviders' => $this->socialProviders(),
        ])->layoutData([
            'sidebar' => true,
            'title' => 'Security',
        ]);
    }
}
