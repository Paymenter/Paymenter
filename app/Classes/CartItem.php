<?php
namespace App\Classes;

class CartItem
{
    public object $product;
    public object $plan;
    public object $configOptions;
    public object $checkoutConfig;
    public Price $price;
    public int $quantity;

    public function __construct($product_id, $plan_id, $configOptions, $checkoutConfig, Price $price, int $quantity)
    {
        $this->product = $product_id;
        $this->plan_id = $plan_id;
        $this->configOptions = (object) $configOptions;
        $this->checkoutConfig = (object) $checkoutConfig;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function __get($name)
    {
        if ($name === 'plan') {
            return \App\Models\Plan::find($this->plan_id);
        }
        if ($name === 'product') {
            return \App\Models\Product::find($this->product);
        }
        return $this->$name ?? null;
    }
}