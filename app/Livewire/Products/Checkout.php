<?php

namespace App\Livewire\Products;

use App\Classes\Cart;
use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use App\Livewire\Traits\CurrencyChanged;
use Livewire\Attributes\On;

class Checkout extends Component 
{
    use CurrencyChanged;

    public $product;

    public Category $category;

    public function mount($product)
    {
        $this->product = $this->category->products()->where('slug', $product)->firstOrFail();
    }

    public function render()
    {
        return view('product.checkout');
    }
}
