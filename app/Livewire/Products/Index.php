<?php

namespace App\Livewire\Products;

use App\Livewire\Component;
use App\Livewire\Traits\CurrencyChanged;
use App\Models\Category;

class Index extends Component
{
    use CurrencyChanged;

    public $products;

    public Category $category;

    public function mount()
    {
        $this->products = $this->category->products()->with('category')->get();
    }

    public function render()
    {
        return view('products.index');
    }
}
