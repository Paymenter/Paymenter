<?php

namespace App\Livewire\Products;

use App\Livewire\Traits\CurrencyChanged;
use App\Models\Category;
use Livewire\Component;

class Show extends Component
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
        return view('product.show');
    }
}
