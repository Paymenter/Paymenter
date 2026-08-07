<?php

namespace App\Livewire\Products;

use App\Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class Show extends Component
{
    public $product;

    public Category $category;

    public function boot()
    {
        \App\Models\ConfigOption::setProductContext($this->product);
        $this->filterConfigOptions();
    }

    protected function filterConfigOptions()
    {
        if ($this->product instanceof \App\Models\Product && $this->product->relationLoaded('configOptions')) {
            $filtered = $this->product->configOptions->filter(function ($option) {
                if (in_array($option->type, ['select', 'radio', 'slider', 'checkbox'])) {
                    return $option->children->isNotEmpty();
                }
                return true;
            });
            $this->product->setRelation('configOptions', $filtered);
        }
    }

    public function mount($product)
    {
        $this->product = $this->category->products()->where('slug', $product)->where('hidden', false)->firstOrFail();
        \App\Models\ConfigOption::setProductContext($this->product);
        $this->product->load(['plans.prices', 'configOptions.children.plans.prices']);
        $this->filterConfigOptions();
    }

    public function render()
    {
        return view('products.show')->layoutData([
            'title' => $this->product->name,
            'image' => $this->product->image ? Storage::url($this->product->image) : null,
        ]);
    }
}
