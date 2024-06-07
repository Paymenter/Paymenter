<?php

namespace App\Livewire\Components;

use App\Models\Currency;
use Livewire\Component;

class CurrencySwitch extends Component
{
    public $currentCurrency;

    public function mount()
    {
        $this->currentCurrency = session('currency', 'USD');
        // Dont render the component if the user is not logged in
        if (auth()->check()) {
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
