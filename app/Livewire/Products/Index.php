<?php

namespace App\Livewire\Products;

use App\Models\Category;
use Livewire\Component;

class Index extends Component
{
    public $products;

    public Category $category;

    public function mount($product)
    {
        $this->products = $this->category->products;
    }

    public function render()
    {
        return view('product.show');
    }
}
