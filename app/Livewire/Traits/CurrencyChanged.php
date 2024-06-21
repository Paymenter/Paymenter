<?php

namespace App\Livewire\Traits;

use Livewire\Attributes\On;

trait CurrencyChanged
{
    #[On('currencyChanged')]
    public function updatedCurrentCurrency()
    {
        $this->dispatch('$refresh');
    }
}
