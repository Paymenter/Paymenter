<?php

namespace App\Livewire\Checkout;

use App\Helpers\ExtensionHelper;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Url;
use Livewire\Component;

class Config extends Component
{
    public Product $product;

    #[Url]
    public $billing_cycle = 'monthly';

    public $userConfig = [];

    public $customConfig = [];

    public $prices;

    public $total = 0;

    #[Url]
    public $config = [];

    public function mount($product)
    {
        $this->product = $product;
        $this->calculate();
    }

    public function calculate()
    {
        $product = $this->product;

        $prices = $product->prices;
        $customConfig = $product->configurableGroups();
        // If billing_cycle isn't set, set it to the lowest billing cycle available
        $billing_cycle = $this->billing_cycle;
        if (!in_array($billing_cycle, ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially'])) {
            $billing_cycle = 'monthly';
        }
        $billing_cycles = ['monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'triennially'];

        foreach ($billing_cycles as $cycle) {
            while ($billing_cycle == $cycle && !$prices->$cycle) {
                $billing_cycle = next($billing_cycles) ?: 'monthly';
                break;
            }
        }

        $this->billing_cycle = $billing_cycle;

        $this->userConfig = empty($this->userConfig) ? ExtensionHelper::getUserConfig($product) : $this->userConfig;
        $this->customConfig = $customConfig;
        $this->prices = $prices;

        // preseed config with default values
        foreach ($customConfig as $group) {
            foreach ($group->configurableOptions()->orderBy('order')->get() as $option) {
                if ($option->type == 'text') {
                    if (!isset($this->config[$option['id']]))
                        $this->config[$option['id']] = '';
                } else {
                    if (!isset($this->config[$option['id']]))
                        $this->config[$option['id']] = $option->configurableOptionInputs()->orderBy('order')->first()->id;
                }
            }
        }

        foreach ($this->userConfig as $key => $config) {
            // If its select, radio or slider, set the default value to the first option
            if (($config->type == 'dropdown' || $config->type == 'radio' || $config->type == 'slider') && !isset($config->value)) {
                $this->userConfig[$key]->value = $config->options[0]->value;
            }
        }

        // Calculate total
        $this->total = $prices->{$this->billing_cycle} ?? $prices->monthly;
        $this->total += $prices->{$this->billing_cycle . '_setup'} ?? 0;
        foreach ($customConfig as $group) {
            foreach ($group->configurableOptions()->orderBy('order')->get() as $option) {
                $configItemInput = $option->configurableOptionInputs()->get();
                foreach ($configItemInput as $configItemInput) {
                    if ($configItemInput->id != $this->config[$option->id] && ($option->type == 'select' || $option->type == 'radio' || $option->type == 'slider')) continue;
                    $configItemPrice = $configItemInput->configurableOptionInputPrice;
                    if ($configItemPrice) {
                        if ($option->type == 'quantity') {
                            $this->total += $configItemPrice->{$this->billing_cycle} * $this->config[$option->id];
                        } else {
                            $this->total += $configItemPrice->{$this->billing_cycle};
                        }
                    }
                }
            }
        }
    }


    public function setBillingCycle($billing_cycle)
    {
        $this->billing_cycle = $billing_cycle;
        $this->calculate();
    }

    public function update($item, $value, $userdConfig = false)
    {
        if ($userdConfig) {
            // Check if item exists in userConfig
            $key = array_search($item, array_column($this->userConfig, 'name'));
            $this->userConfig[$key]->value = $value;
            $this->calculate();
            return;
        }
        $this->config[$item] = $value;
        $this->calculate();
    }

    public function checkout()
    {
        $prices = $this->product->prices;
        $userConfig = ExtensionHelper::getUserConfig($this->product);

        $customConfig = $this->customConfig;
        $product = [];

        $config = [];
        foreach ($userConfig as $uconfig) {
            $key = array_search($uconfig->name, array_column($this->userConfig, 'name'));

            $this->validateConfigItem($uconfig, $this->userConfig[$key]->value);
            $config[$uconfig->name] = $this->userConfig[$key]->value;
        }

        $product['config'] = $config;
        $product['setup_fee'] = 0;
        if ($prices->type == 'recurring') {
            $product['price'] = $prices->{$this->billing_cycle} ?? $prices->monthly;
            $product['billing_cycle'] = $this->billing_cycle;
            $product['setup_fee'] = $prices->{$this->billing_cycle . '_setup'} ?? 0;
        } else if (
            $prices->type == 'one-time'
        ) {
            $product['price'] = $this->product->prices()->get()->first()->monthly;
        } else {
            $product['price'] = 0;
        }

        $configItems = [];
        foreach ($customConfig as $cConfig) {
            $configItemsGet = $cConfig->configurableOptions()->get();
            foreach ($configItemsGet as $configItem) {
                if ($configItem->hidden) continue;
                if (!$this->config[$configItem->id]) {
                    return redirect()->back()->with('error', $configItem->name . ' is required');
                }
                $configItems[$configItem->id] = $this->config[$configItem->id];
                $configItemInput = $configItem->configurableOptionInputs()->get();
                foreach ($configItemInput as $configItemInput) {
                    if ($configItem->type == 'quantity') {
                        if (!$this->config[$configItem->id]) continue;
                    }
                    if ($configItemInput->id != $this->config[$configItem->id] && ($configItem->type == 'select' || $configItem->type == 'radio' || $configItem->type == 'slider')) continue;
                    $configItemPrice = $configItemInput->configurableOptionInputPrice;
                    if ($configItemPrice) {
                        if ($configItem->type == 'quantity') {
                            $product['price'] += $configItemPrice->{$this->billing_cycle} * $this->config[$configItem->id];
                        } else {
                            $product['price'] += $configItemPrice->{$this->billing_cycle};
                        }
                        $product['setup_fee'] += $configItemPrice->{$this->billing_cycle . '_setup'};
                    }
                }
            }
        }

        $product['configurableOptions'] = $configItems;
        $product['config'] = $config;
        $product['product_id'] = $this->product->id;
        $product['quantity'] = 1;

        // Add to cart
        $cart = session()->get('cart', []);
        $cart[] = $product;
        session()->put('cart', $cart);

        return redirect()->route('checkout.index');
    }

    public function validateConfigItem($config, $value)
    {
        if (isset($config->required) && $config->required) {
            if (!isset($config->validation)) {
                $config->validation = 'required';
            } else {
                $config->validation .= '|required';
            }
        }

        if (isset($config->validation) && $config->validation) {
            return $this->validateOnly($config->name, [$config->name => $config->validation], [], [], [$config->name => $value]);
        }

        return true;
    }

    public function render()
    {
        return view('livewire.checkout.config');
    }
}
