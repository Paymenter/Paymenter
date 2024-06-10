<?php

namespace App\Classes;

/**
 * Class Price
 * @package App\Classes
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

    /**
     * Price constructor.
     * @param $priceAndCurrency
     */
    public function __construct($priceAndCurrency)
    {
        $this->price = $priceAndCurrency->price->price;
        $this->currency = $priceAndCurrency->currency;
        $this->setup_fee = $priceAndCurrency->price->setup_fee;
        $this->has_setup_fee = $priceAndCurrency->price->setup_fee > 0;
    }

    public function format($price)
    {
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
        \Debugbar::info('Property ' . $name . ' not found');
        if ($name == 'price') {
            return $this->price;
        }
        if ($name == 'currency') {
            return $this->currency;
        }
        if ($name == 'available') {
            return $this->currency ? true : false;
        }
        if ($name == 'setup_fee') {
            return $this->format($this->setup_fee);
        }
    }
}
