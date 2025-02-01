<?php

namespace App\Livewire\Components;

use App\Livewire\Component;

class LanguageSwitch extends Component
{
    public $currentLocale;

    public function mount()
    {
        $this->currentLocale = session('locale', config('app.locale'));
    }

    public function updatedCurrentLocale($locale)
    {
        if (!array_key_exists($locale, config('app.available_locales', []))) {
            return;
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return $this->redirect(request()->header('Referer', '/'), navigate: true);
    }

    public function render()
    {
        $locales = config('app.available_locales');

        return view('components.language-switch', compact('locales'));
    }
}
