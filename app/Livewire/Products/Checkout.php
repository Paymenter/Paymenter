<?php

namespace App\Livewire\Products;

use App\Classes\Cart;
use App\Classes\Price;
use App\Livewire\Component;
use App\Livewire\Traits\CurrencyChanged;
use App\Models\Category;
use App\Models\Plan;
use App\Models\Price as ModelsPrice;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

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

    public $setup_fee;

    #[Url(keep: true, as: 'options')]
    public $configOptions = [];

    #[Url(as: 'edit'), Locked]
    public $cartProductKey = null;

    public function mount($product)
    {
        $this->product = $this->category->products()->where('slug', $product)->firstOrFail();

        // Is there a existing item in the cart?
        if (Cart::get()->has($this->cartProductKey) && Cart::get()->get($this->cartProductKey)->product->id === $this->product->id) {
            $item = Cart::get()->get($this->cartProductKey);
            // Get the item from the cart
            $this->plan = $item->plan->fresh();
            $this->plan_id = $this->plan->id;
            $this->configOptions = $item->configOptions->mapWithKeys(function ($option) {
                return [$option->option_id => $option->value];
            });
        } else {
            // Set the first plan as default
            $this->plan = $this->product->plans->first();

            // Prepare the config options
            $this->configOptions = $this->product->configOptions->mapWithKeys(function ($option) {
                if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                    return [$option->id => $this->configOptions[$option->id] ?? null];
                }

                return [$option->id => $this->configOptions[$option->id] ?? $option->children->first()->id];
            });
        }
        // Update the pricing
        $this->updatePricing();
    }

    // Making sure its being called when the currency is changed
    #[On('currencyChanged')]
    public function updatePricing()
    {
        $total = $this->plan->price()->price + $this->product->configOptions->sum(function ($option) {
            if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                return $option->children->first()?->price(billing_period: $this->plan->billing_period, billing_unit: $this->plan->billing_unit)->price;
            }

            return $option->children->where('id', $this->configOptions[$option->id])->first()?->price(billing_period: $this->plan->billing_period, billing_unit: $this->plan->billing_unit)->price;
        });
        $setup_fee = $this->plan->price()->setup_fee + $this->product->configOptions->sum(function ($option) {
            if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                return $option->children->first()?->price(billing_period: $this->plan->billing_period, billing_unit: $this->plan->billing_unit)->setup_fee;
            }

            return $option->children->where('id', $this->configOptions[$option->id])->first()?->price(billing_period: $this->plan->billing_period, billing_unit: $this->plan->billing_unit)->setup_fee;
        });
        $this->total = new Price([
            'price' => $total + $setup_fee,
            'currency' => $this->plan->price()->currency,
            'setup_fee' => $setup_fee,
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

        // Change configOptions so they also contain the name of the option (resulting in less database calls = faster speeds) e.g. ['option_id' => 'x', 'option_name' => 'y', 'value' => 'z', 'value_name' => 'a']
        $configOptions = $this->product->configOptions->map(function ($option) {
            if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                return (object) ['option_id' => $option->id, 'option_name' => $option->name, 'value' => $this->configOptions[$option->id], 'value_name' => $this->configOptions[$option->id]];
            }

            return (object) ['option_id' => $option->id, 'option_name' => $option->name, 'value' => $this->configOptions[$option->id], 'value_name' => $option->children->where('id', $this->configOptions[$option->id])->first()->name];
        });

        Cart::add($this->product, $this->plan, $configOptions, $this->total, key: $this->cartProductKey);

        $this->dispatch('cartUpdated');

        return $this->redirect(route('cart'), true);
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
