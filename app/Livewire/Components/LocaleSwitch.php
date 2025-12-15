<?php

namespace App\Livewire\Components;

use App\Classes\Cart;
use App\Livewire\Component;

class LocaleSwitch extends Component
{
    public $currentLocale;

    public $currentCurrency;

    protected $currencies = [];

    public function mount()
    {
        $this->currentLocale = session('locale', config('app.locale'));
        $this->currentCurrency = session('currency', config('settings.default_currency'));
        $this->currencies = \App\Models\Currency::all()->map(fn ($currency) => [
            'value' => $currency->code,
            'label' => $currency->name,
        ])->values()->toArray();

        if ((count($this->currencies) <= 1 || Cart::items()->count() > 0) && count(config('settings.allowed_languages', [])) <= 1) {
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

    public function updatedCurrentLocale($locale)
    {
        if (!in_array($locale, config('settings.allowed_languages', []))) {
            $this->notify('The selected language is not available.', 'error');

            return;
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return $this->redirect(request()->header('Referer', '/'), navigate: true);
    }

    public function render()
    {
        $locales = config('settings.allowed_languages');

        return view('components.locale-switch', compact('locales'));
    }
}
