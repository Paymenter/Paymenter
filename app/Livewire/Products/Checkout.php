<?php

namespace App\Livewire\Products;

use App\Livewire\Traits\CurrencyChanged;
use App\Models\Category;
use Livewire\Component;

class Checkout extends Component
{
    use CurrencyChanged;

    public $product;

    public Category $category;

    public $plan;

    public function mount($product)
    {
        $this->product = $this->category->products()->where('slug', $product)->firstOrFail();
        $this->plan = $this->product->plans->first()->id;
    }

    public function render()
    {
        return view('product.checkout');
    }
}
