<?php

namespace App\Classes;

/**
 * Class Price
 */
class Price
{
    /**
     * @var int
     */
    public $price;

    /**
     * @var mixed
     */
    public $currency;

    /**
     * @var mixed
     */
    public $setup_fee;

    public $has_setup_fee;

    public $is_free;

    /**
     * Price constructor.
     */
    public function __construct($priceAndCurrency = null, $free = false)
    {
        if ($free) {
            $this->price = 0;
            $this->currency = null;
            $this->is_free = true;

            return;
        }

        $this->price = $priceAndCurrency->price->price ?? null;
        $this->currency = $priceAndCurrency->currency;
        $this->setup_fee = $priceAndCurrency->price->setup_fee ?? null;
        $this->has_setup_fee = isset($priceAndCurrency->price->setup_fee) ? $priceAndCurrency->price->setup_fee > 0 : false;
    }

    public function format($price)
    {
        if ($this->is_free) {
            return 'Free';
        }
        if (!$this->currency) {
            return 'Not available in your currency';
        }

        return $this->currency->prefix . number_format($price, 2) . $this->currency->suffix;
    }

    public function __toString()
    {
        return $this->format($this->price);
    }

    public function __get($name)
    {
        if ($name == 'price') {
            return $this->price;
        }
        if ($name == 'currency') {
            return $this->currency;
        }
        if ($name == 'available') {
            return $this->currency || $this->is_free ? true : false;
        }
        if ($name == 'setup_fee') {
            return $this->format($this->setup_fee);
        }
    }
}
