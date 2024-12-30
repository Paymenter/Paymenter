<?php

namespace Paymenter\Extensions\Others\Affiliates\Livewire\Affiliates;

use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Affiliate extends Component
{
    public string $referral_code = '';

    public function rules()
    {
        return [
            'referral_code' => 'required|string|unique|min:5|max:25',
        ];
    }

    public function signup()
    {
        dd('WIP');
        $this->validate();

        $this->notify(__('affiliate.signup-success'));
    }

    public function render()
    {
        $affiliate = Auth::user()->affiliate;
        $extension = ExtensionHelper::getExtension('other', 'Affiliates');

        return view('affiliates::index', [
            'affiliate' => $affiliate,
            'signup_type' => $extension->config('type')
        ]);
    }
}
