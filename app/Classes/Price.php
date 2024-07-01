<?php

namespace App\Classes;

/**
 * Class Price
 */
class Price
{
    public $price;

    public $currency;

    public $setup_fee;

    public $has_setup_fee;

    public $is_free;

    public $dontShowUnavailablePrice;

    public object $formatted;

    public function __construct($priceAndCurrency = null, $free = false, $dontShowUnavailablePrice = false)
    {
        if (is_array($priceAndCurrency)) {
            $priceAndCurrency = (object) $priceAndCurrency;
        }
        if ($free) {
            $this->price = 0;
            $this->currency = null;
            $this->is_free = true;

            return;
        }

        $this->price = $priceAndCurrency->price->price ?? $priceAndCurrency->price ?? null;
        $this->currency = $priceAndCurrency->currency;
        if (is_array($this->currency)) {
            $this->currency = (object) $this->currency;
        }
        $this->setup_fee = $priceAndCurrency->price->setup_fee ?? $priceAndCurrency->setup_fee ?? null;
        $this->has_setup_fee = isset($this->setup_fee) ? $this->setup_fee > 0 : false;
        $this->dontShowUnavailablePrice = $dontShowUnavailablePrice;
        $this->formatted = (object) [
            'price' => $this->format($this->price),
            'setup_fee' => $this->format($this->setup_fee),
        ];
    }

    private function format($price)
    {
        if ($this->is_free) {
            return 'Free';
        }
        if (!$this->currency) {
            if ($this->dontShowUnavailablePrice) {
                return '';
            }

            return 'Not available in your currency';
        }

        return $this->currency->prefix . number_format($price, 2) . $this->currency->suffix;
    }

    public function __toString()
    {
        return $this->formatted->price;
    }

    public function __get($name)
    {
        if ($name == 'available') {
            return $this->currency || $this->is_free ? true : false;
        } else {
            return $this->$name;
        }
    }
}
