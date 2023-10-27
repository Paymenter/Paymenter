<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    public $cartItems = 0;

    #[On('updateCart')]
    public function mount()
    {
        $cart = session()->get('cart');
        if ($cart) {
            $this->cartItems = count($cart);
        }
    }

    public function render()
    {
        return view('livewire.cart-count');
    }
}
