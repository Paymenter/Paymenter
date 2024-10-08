<?php

namespace App\Livewire\Components;

use App\Classes\Cart;
use App\Livewire\Component;
use App\Models\Currency;

class CurrencySwitch extends Component
{
    public $currentCurrency;

    public function mount()
    {
        $this->currentCurrency = session('currency', config('settings.default_currency'));

        if (Cart::get()->isNotEmpty()) {
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
        $this->dispatch('currencyChanged', $currency);
    }

    public function render()
    {
        $currencies = Currency::all();

        return view('components.currency-switch', compact('currencies'));
    }
}
