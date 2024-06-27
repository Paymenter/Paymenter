<?php

namespace App\Livewire\Products;

use App\Livewire\Traits\CurrencyChanged;
use App\Models\Category;
use App\Models\Plan;
use Livewire\Attributes\Url;
use Livewire\Component;

class Checkout extends Component
{
    use CurrencyChanged;

    public $product;

    public Category $category;

    public Plan $plan;

    public $plan_id;

    #[Url(keep: true, as: 'options')]
    public $configOptions = [];

    public function mount($product)
    {
        $this->product = $this->category->products()->where('slug', $product)->firstOrFail();
        $this->plan = $this->product->plans->first();

        $this->configOptions = $this->product->configOptions->mapWithKeys(function ($option) {
            return [$option->id => $option->children->first()->id];
        });
    }

    // On change of the plan, update the config options
    public function updatedPlanId($value)
    {
        $this->plan = Plan::find($value);
    }

    public function render()
    {
        return view('product.checkout');
    }
}
