<?php

namespace App\Livewire\Components;

use App\Classes\Cart;
use App\Models\Currency;
use Livewire\Component;

class CurrencySwitch extends Component
{
    public $currentCurrency;

    public function mount()
    {
        $this->currentCurrency = session('currency', 'USD');

        if (Cart::get()->isNotEmpty()) {
            $this->skipRender();
        }
    }

    public function updatedCurrentCurrency($currency)
    {
        session(['currency' => $currency]);
        $this->dispatch('currencyChanged', $currency);
    }

    public function render()
    {
        $currencies = Currency::all();

        return view('components.currency-switch', compact('currencies'));
    }
}
