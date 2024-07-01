<?php

namespace App\Livewire\Products;

use App\Classes\Cart;
use App\Classes\Price;
use App\Livewire\Traits\CurrencyChanged;
use App\Models\Category;
use App\Models\Plan;
use App\Models\Price as ModelsPrice;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;

class Checkout extends Component
{
    use CurrencyChanged;

    public $product;

    public Category $category;

    public Plan $plan;

    public $plan_id;

    // Don't allow the user to change the total via hacks
    #[Locked]
    public $total;

    #[Url(keep: true, as: 'options')]
    public $configOptions = [];

    public function mount($product)
    {
        $this->product = $this->category->products()->where('slug', $product)->firstOrFail();
        $this->plan = $this->product->plans->first();

        $this->configOptions = $this->product->configOptions->mapWithKeys(function ($option) {
            if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                return [$option->id => null];
            }

            return [$option->id => $this->configOptions[$option->id] ?? $option->children->first()->id];
        });
        $this->updatePricing();
    }

    // Making sure its being called when the currency is changed
    #[On('currencyChanged')]
    public function updatePricing()
    {
        $this->total = new Price([
            'price' => $this->plan->price()->price + $this->product->configOptions->sum(function ($option) {
                if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                    return $option->children->first()?->price(billing_period: $this->plan->billing_period, billing_unit: $this->plan->billing_unit)->price;
                }

                return $option->children->where('id', $this->configOptions[$option->id])->first()?->price(billing_period: $this->plan->billing_period, billing_unit: $this->plan->billing_unit)->price;
            }),
            'currency' => $this->plan->price()->currency,
        ]);
    }

    // On change of the plan, update the config options
    public function updatedPlanId($value)
    {
        $this->plan = Plan::findOrFail($value);
        $this->updatePricing();
    }

    // On change of the config options, update the pricing
    public function updatedConfigOptions()
    {
        $this->updatePricing();
    }

    public function rules()
    {
        $rules = [];
        foreach ($this->product->configOptions as $option) {
            if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                $rules["configOptions.{$option->id}"] = ['required'];
            } else {
                $rules["configOptions.{$option->id}"] = ['required', 'exists:config_options,id'];
            }
        }

        return $rules;
    }

    public function attributes()
    {
        $messages = [];
        foreach ($this->product->configOptions as $option) {
            $messages["configOptions.{$option->id}"] = $option->name;
        }

        return $messages;
    }

    public function checkout()
    {
        // Do the checkout
        // First we validate the plans
        $this->validate(attributes: $this->attributes());

        Cart::add($this->product, $this->plan, $this->configOptions, $this->total);

        $this->dispatch('cartUpdated');

        return redirect()->route('cart');
    }

    private function total()
    {
        // A class isn't allowed to be a Livewire property
        $totalPrice['price'] = new ModelsPrice(['price' => $this->total]);
        $totalPrice['currency'] = $this->plan->price()->currency;
        $totalPrice = new Price((object) $totalPrice);

        return $totalPrice;
    }

    public function render()
    {
        return view('product.checkout');
    }
}
