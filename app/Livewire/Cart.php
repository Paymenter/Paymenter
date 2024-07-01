<?php

namespace App\Livewire;

use App\Classes\Cart as ClassesCart;
use App\Classes\Price;
use Livewire\Component;

class Cart extends Component
{
    public $items;

    public $total;

    public function mount()
    {
        $this->items = ClassesCart::get()->map(function ($item) {
            return (object) $item;
        });
        $this->updateTotal();
    }

    private function updateTotal()
    {
        if ($this->items->isEmpty()) {
            $this->total = null;

            return;
        }
        $this->total = new Price(['price' => $this->items->sum(fn ($item) => $item->price->price), 'currency' => $this->items->first()->price->currency]);
    }

    public function removeProduct($index)
    {
        ClassesCart::remove($index);
        $this->items = ClassesCart::get()->map(function ($item) {
            return (object) $item;
        });
        $this->updateTotal();
    }

    public function render()
    {
        return view('cart');
    }
}
