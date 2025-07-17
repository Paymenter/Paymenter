<?php

namespace Paymenter\Extensions\Others\Affiliates\Livewire\Affiliates;

use App\Helpers\ExtensionHelper;
use App\Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Paymenter\Extensions\Others\Affiliates\Models\Affiliate as AffiliateModel;

class Affiliate extends Component
{
    public string $signup_type = 'custom';

    public ?AffiliateModel $affiliate;

    public string $referral_code = '';

    public function mount()
    {
        $this->affiliate = Auth::user()->affiliate;
        $this->signup_type = ExtensionHelper::getExtension('other', 'Affiliates')->config('type');
    }

    public function render()
    {
        return view('affiliates::index')->layoutData([
            'sidebar' => true,
        ]);
    }

    public function signup()
    {
        $this->validate();

        /**
         * @var User
         */
        $user = Auth::user();
        if ($user->affiliate) {
            $this->notify(__('affiliates::affiliate.you-are-already-affiliated'));

            return;
        }

        $this->affiliate = $user->affiliate()->create([
            'code' => $this->signup_type === 'custom' ? $this->referral_code : Str::random(10),
            'visitors' => 0,
            'reward' => null,
            'discount' => null,
        ])->refresh();

        $this->notify(__('affiliates::affiliate.signup-success'));
    }

    public function rules()
    {
        return [
            'referral_code' => [Rule::requiredIf($this->signup_type === 'custom'), 'alpha_num:ascii', 'unique:ext_affiliates,code', 'min:5', 'max:25'],
        ];
    }
}
