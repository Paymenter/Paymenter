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
        $this->products = $this->category->products()->where('hidden', false)->with('category')->orderBy('sort')->get();
        $this->childCategories = $this->category->children()->where(function ($query) {
            $query->whereHas('children')->orWhereHas('products', function ($query) {
                $query->where('hidden', false);
            });
        })->orderBy('sort')->get();
        $this->categories = Category::whereNull('parent_id')->where(function ($query) {
            $query->whereHas('children')->orWhereHas('products', function ($query) {
                $query->where('hidden', false);
            });
        })->orderBy('sort')->get();
    }

    public function render()
    {
        return view('products.index');
    }
}
