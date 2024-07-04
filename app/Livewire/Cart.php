<?php

namespace App\Livewire;

use App\Classes\Cart as ClassesCart;
use App\Classes\Price;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Cart extends Component
{
    #[Locked]
    public $items;

    #[Locked]
    public $total;

    public function mount()
    {
        $this->items = ClassesCart::get();
        $this->updateTotal();
    }

    private function updateTotal()
    {
        if ($this->items->isEmpty()) {
            $this->total = null;

            return;
        }
        $this->total = new Price(['price' => $this->items->sum(fn ($item) => $item->price->price * $item->quantity), 'currency' => $this->items->first()->price->currency]);
    }

    public function removeProduct($index)
    {
        ClassesCart::remove($index);
        $this->items = ClassesCart::get()->map(function ($item) {
            return (object) $item;
        });
        $this->updateTotal();
    }

    public function updateQuantity($index, $quantity)
    {
        if ($this->items->get($index)->product->allow_quantity !== 'combined') {
            return;
        }
        if ($quantity < 1) {
            $this->removeProduct($index);

            return;
        }
        $this->items->get($index)->quantity = $quantity;
        session(['cart' => $this->items->toArray()]);
        $this->updateTotal();
    }

    // Checkout
    public function checkout()
    {
        if ($this->items->isEmpty()) {
            return;
        }

    }

    public function render()
    {
        return view('cart');
    }
}
