<?php

namespace App\Livewire\Products;

use App\Classes\Cart;
use App\Classes\Price;
use App\Helpers\ExtensionHelper;
use App\Livewire\Component;
use App\Models\Category;
use App\Models\Plan;
use App\Models\Price as ModelsPrice;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

class Checkout extends Component
{
    public $product;

    public Category $category;

    public Plan $plan;

    #[Url(keep: true, as: 'plan')]
    public $plan_id;

    // Don't allow the user to change the total via hacks
    #[Locked]
    public $total;

    public $setup_fee;

    #[Url(keep: true, as: 'options')]
    public $configOptions = [];

    #[Url(keep: true, as: 'config')]
    public $checkoutConfig = [];

    #[Url(as: 'edit'), Locked]
    public $cartProductKey = null;

    public function mount($product)
    {
        $this->product = $this->category->products()->where('slug', $product)->firstOrFail();
        if ($this->product->stock === 0) {
            return $this->redirect(route('products.show', ['category' => $this->category, 'product' => $this->product]), true);
        }

        // Is there a existing item in the cart?
        if (Cart::get()->has($this->cartProductKey) && Cart::get()->get($this->cartProductKey)->product->id === $this->product->id) {
            $item = Cart::get()->get($this->cartProductKey);
            // Get the item from the cart
            $this->plan = $item->plan->fresh();
            $this->plan_id = $this->plan->id;
            $this->configOptions = $item->configOptions->mapWithKeys(function ($option) {
                return [$option->option_id => $option->value];
            });
            $this->checkoutConfig = (array) $item->checkoutConfig;
        } else {
            // Set the first plan as default
            $this->plan = $this->plan_id ? $this->product->plans->findOrFail($this->plan_id) : $this->product->plans->first();
            $this->plan_id = $this->plan->id;

            // Prepare the config options
            $this->configOptions = $this->product->configOptions->mapWithKeys(function ($option) {
                if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                    return [$option->id => $this->configOptions[$option->id] ?? null];
                }

                return [$option->id => $this->configOptions[$option->id] ?? $option->children->first()->id];
            })->toArray();
            foreach ($this->getCheckoutConfig() as $config) {
                if (in_array($config['type'], ['select', 'radio'])) {
                    $this->checkoutConfig[$config['name']] = $this->checkoutConfig[$config['name']] ?? $config['default'] ?? array_key_first($config['options']);
                } else {
                    $this->checkoutConfig[$config['name']] = $this->checkoutConfig[$config['name']] ?? $config['default'] ?? null;
                }
            }
        }
        // Update the pricing
        $this->updatePricing();

        // As there is only one plan, config options and checkout config, we can directly call the checkout method to avoid confusion
        // This is only done when the user is not editing the cart item
        if ($this->product->plans->count() === 1 && empty($this->configOptions) && empty($this->checkoutConfig)) {
            $this->checkout();
        }
    }

    public function updatePricing()
    {
        $total = $this->plan->price()->price + $this->product->configOptions->sum(function ($option) {
            if ($option->type === 'checkbox' && (isset($this->configOptions[$option->id]) && $this->configOptions[$option->id])) {
                return $option->children->first()?->price(billing_period: $this->plan->billing_period, billing_unit: $this->plan->billing_unit)->price;
            }
            if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                return 0;
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
        ], apply_exclusive_tax: true);
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

    public function getCheckoutConfig()
    {
        return once(fn () => ExtensionHelper::getCheckoutConfig($this->product, $this->checkoutConfig));
    }

    public function rules()
    {
        $rules = [
            'plan_id' => [
                'required',
                Rule::exists('plans', 'id')->where(function ($query) {
                    $query->where('priceable_id', $this->product->id)->where('priceable_type', get_class($this->product));
                }),
            ],
        ];
        foreach ($this->product->configOptions as $option) {
            if (in_array($option->type, ['text', 'number'])) {
                $rules["configOptions.{$option->id}"] = ['required'];
            } elseif ($option->type === 'checkbox') {
            } else {
                $rules["configOptions.{$option->id}"] = ['required', 'exists:config_options,id'];
            }
        }
        foreach ($this->getCheckoutConfig() as $key => $config) {
            $validationRules = [];
            if ($config['required'] ?? false) {
                $validationRules[] = 'required';
            }
            if (isset($config['type'])) {
                switch ($config['type']) {
                    case 'text':
                    case 'number':
                        $validationRules[] = 'string';
                        break;
                    case 'select':
                    case 'radio':
                        $validationRules[] = 'in:' . implode(',', array_keys($config['options']));
                        break;
                    case 'checkbox':
                        $validationRules[] = 'nullable';
                        $validationRules[] = 'boolean';
                        break;
                }
            }
            if (isset($config['validation'])) {
                if (is_array($config['validation'])) {
                    $validationRules = array_merge($validationRules, $config['validation']);
                } else {
                    // Is validation seperated by |?
                    $validationRules = array_merge($validationRules, explode('|', $config['validation']));
                }
            }
            if (count($validationRules) > 0) {
                $rules["checkoutConfig.{$config['name']}"] = $validationRules;
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
        foreach ($this->getCheckoutConfig() as $key => $config) {
            $messages["checkoutConfig.{$config['name']}"] = $config['label'] ?? $config['name'];
        }

        return $messages;
    }

    public function checkout()
    {
        // Do the checkout
        // First we validate the plans
        $this->validate(attributes: $this->attributes());

        // Change configOptions so they also contain the name of the option (resulting in less database calls = faster speeds)
        $configOptions = $this->product->configOptions->map(function ($option) {
            if (in_array($option->type, ['text', 'number', 'checkbox'])) {
                return (object) ['option_id' => $option->id, 'option_name' => $option->name, 'option_type' => $option->type, 'option_env_variable' => $option->env_variable, 'value' => $this->configOptions[$option->id], 'value_name' => $this->configOptions[$option->id]];
            }

            return (object) ['option_id' => $option->id, 'option_name' => $option->name, 'option_type' => $option->type, 'option_env_variable' => $option->env_variable, 'value' => $this->configOptions[$option->id], 'value_name' => $option->children->where('id', $this->configOptions[$option->id])->first()->name];
        });

        Cart::add($this->product, $this->plan, $configOptions, $this->checkoutConfig, $this->total, key: $this->cartProductKey);

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
        return view('products.checkout')->layoutData([
            'title' => $this->product->name,
            'image' => $this->product->image ? Storage::url($this->product->image) : null,
        ]);
    }
}
