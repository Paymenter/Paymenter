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
     * Price constructor.
     * @param $priceAndCurrency
     */
    public function __construct($priceAndCurrency)
    {
        $this->price = $priceAndCurrency->price;
        $this->currency = $priceAndCurrency->currency;
    }

    public function format()
    {
        if (!$this->currency) {
            return 'Not available in your currency';
        }
        return $this->currency->prefix . number_format($this->price, 2) . $this->currency->suffix;
    }

    public function __toString()
    {
        return $this->format();
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
            return $this->currency ? true : false;
        }
    }
}
