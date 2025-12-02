<?php

namespace App\Livewire\Client;

use App\Livewire\Component;
use App\Models\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
                Session::where('user_id', Auth::id())
                    ->where('id', '!=', session()->getId())
                    ->delete();
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

    public function logoutSession(Session $session)
    {
        if ($session->user_id !== Auth::id()) {
            $this->notify(__('Unauthorized'), 'error');

            return;
        }

        $session->delete();

        $this->notify(__('account.notifications.session_logged_out'));
    }

    public function render()
    {
        return view('client.account.security')->layoutData([
            'sidebar' => true,
            'title' => 'Security',
        ]);
    }
}
