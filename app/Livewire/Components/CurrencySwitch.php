<?php

namespace App\Livewire\Components;

use App\Classes\Cart;
use App\Livewire\Component;
use App\Models\Currency;

class CurrencySwitch extends Component
{
    public $currentCurrency;

    protected $currencies = [];

    public function mount()
    {
        $this->currentCurrency = session('currency', config('settings.default_currency'));
        $this->currencies = Currency::all()->map(fn ($currency) => [
            'value' => $currency->code,
            'label' => $currency->name,
        ])->values()->toArray();
        if (Cart::get()->isNotEmpty() || count($this->currencies) <= 1) {
            $this->skipRender();
        }
    }

    public function updatedCurrentCurrency($currency)
    {
        if (Cart::get()->isNotEmpty()) {
            $this->notify('You cannot change the currency while there are items in the cart.', 'error');
            $this->currentCurrency = session('currency', config('settings.default_currency'));

            return;
        }
        session(['currency' => $currency]);

        return $this->redirect(request()->header('Referer', '/'), navigate: true);
    }

    public function render()
    {
        return view('components.currency-switch');
    }
}
