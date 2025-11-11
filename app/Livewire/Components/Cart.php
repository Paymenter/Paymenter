<?php

namespace App\Livewire\Components;

use App\Classes\Cart as ClassesCart;
use App\Livewire\Component;
use Livewire\Attributes\On;

class Cart extends Component
{
    public $cartCount;

    public function mount()
    {
        $this->cartCount = ClassesCart::items()->count();
        if ($this->cartCount === 0) {
            $this->skipRender();
        }
    }

    #[On('cartUpdated')]
    public function onCartUpdated()
    {
        $this->cartCount = ClassesCart::items()->count();
    }

    public function render()
    {
        return view('components.cart');
    }
}
