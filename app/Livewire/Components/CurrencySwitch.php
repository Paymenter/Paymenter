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
        if (Cart::items()->count() > 0 || count($this->currencies) <= 1) {
            $this->skipRender();
        }
    }

    public function updatedCurrentCurrency($currency)
    {
        $this->validate([
            'currentCurrency' => 'required|exists:currencies,code',
        ]);
        if (Cart::items()->count() > 0) {
            $this->notify('You cannot change the currency while there are items in the cart.', 'error');
            $this->currentCurrency = session('currency', config('settings.default_currency'));

            return;
        }
        session(['currency' => $currency]);
        $cart = Cart::get();
        if ($cart->exists) {
            $cart->currency_code = $currency;
            $cart->save();
        }

        return $this->redirect(request()->header('Referer', '/'), navigate: true);
    }

    public function render()
    {
        return view('components.currency-switch');
    }
}
