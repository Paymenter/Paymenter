<?php

namespace App\Livewire\Products;

use App\Livewire\Component;
use App\Livewire\Traits\CurrencyChanged;
use App\Models\Category;

class Show extends Component
{
    use CurrencyChanged;

    public $product;

    public Category $category;

    public function mount($product)
    {
        $this->product = $this->category->products()->where('slug', $product)->where('hidden', false)->firstOrFail();
    }

    public function render()
    {
        return view('products.show');
    }
}
