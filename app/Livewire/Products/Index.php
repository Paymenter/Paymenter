<?php

namespace App\Livewire\Products;

use App\Livewire\Component;
use App\Models\Category;

class Index extends Component
{
    public $products;

    public Category $category;

    public function mount()
    {
        $this->products = $this->category->products;
    }

    public function render()
    {
        return view('product.index');
    }
}
