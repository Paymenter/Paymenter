<?php

namespace App\Livewire\Products;

use App\Livewire\Component;
use App\Livewire\Traits\CurrencyChanged;
use App\Models\Category;

class Index extends Component
{
    use CurrencyChanged;

    public $products;

    public $categories;

    public $childCategories;

    public Category $category;

    public function mount()
    {
        $this->products = $this->category->products()->with('category')->get();
        $this->childCategories = $this->category->children()->where(function ($query) {
            $query->whereHas('children')->orWhereHas('products');
        })->get();
        $this->categories = Category::whereNull('parent_id')->where(function ($query) {
            $query->whereHas('children')->orWhereHas('products');
        })->get();
    }

    public function render()
    {
        return view('products.index');
    }
}
