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

    public $tax = 0;

    public $setup_fee_tax = 0;

    public $discount = 0;

    public $original_price;

    public $original_setup_fee;

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    public function __construct($priceAndCurrency = null, $free = false, $dontShowUnavailablePrice = false, $apply_exclusive_tax = false)
    {
        if (is_array($priceAndCurrency)) {
            $priceAndCurrency = (object) $priceAndCurrency;
        }
        if ($free) {
            $this->price = 0;
            $this->currency = $priceAndCurrency->currency ?? null;
            $this->is_free = true;

            $this->formatted = (object) [
                'price' => $this->format($this->price),
                'setup_fee' => $this->format($this->setup_fee),
                'tax' => $this->format($this->tax),
                'setup_fee_tax' => $this->format($this->setup_fee_tax),
            ];

            return;
        }

        $this->price = $priceAndCurrency->price->price ?? $priceAndCurrency->price ?? null;
        $this->currency = $priceAndCurrency->currency;
        if (is_array($this->currency)) {
            $this->currency = (object) $this->currency;
        }
        $this->setup_fee = $priceAndCurrency->price->setup_fee ?? $priceAndCurrency->setup_fee ?? null;
        // We save the original so we can revert back to it when removing a coupon
        $this->original_price = $this->price;
        $this->original_setup_fee = $this->setup_fee;

        // Calculate taxes
        if (config('settings.tax_enabled')) {
            $tax = Settings::tax();
            if ($tax) {
                // Inclusive has the tax included in the price
                if (config('settings.tax_type') == 'inclusive' || !$apply_exclusive_tax) {
                    $this->tax = number_format($this->price - ($this->price / (1 + $tax->rate / 100)), 2, '.', '');
                    if ($this->setup_fee) {
                        $this->setup_fee_tax = number_format($this->setup_fee - ($this->setup_fee / (1 + $tax->rate / 100)), 2, '.', '');
                    }
                } else {
                    // Exclusive has the tax added to the price as an extra
                    $this->tax = number_format($this->price * $tax->rate / 100, 2, '.', '');
                    $this->original_price = $this->price + $this->tax;
                    $this->price = number_format($this->price + $this->tax, 2, '.', '');
                    if ($this->setup_fee) {
                        $this->setup_fee_tax = number_format($this->setup_fee * $tax->rate / 100, 2, '.', '');
                        $this->original_setup_fee = $this->setup_fee + $this->setup_fee_tax;
                        $this->setup_fee = number_format($this->setup_fee + $this->setup_fee_tax, 2, '.', '');
                    }
                }
            }
        }
        $this->has_setup_fee = isset($this->setup_fee) ? $this->setup_fee > 0 : false;
        $this->dontShowUnavailablePrice = $dontShowUnavailablePrice;
        $this->formatted = (object) [
            'price' => $this->format($this->price),
            'setup_fee' => $this->format($this->setup_fee),
            'tax' => $this->format($this->tax),
            'setup_fee_tax' => $this->format($this->setup_fee_tax),
        ];
    }

    public function format($price)
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
        // Get the format
        $format = $this->currency->format;
        switch ($format) {
            case '1.000,00':
                $price = number_format($price, 2, ',', '.');
                break;
            case '1,000.00':
                $price = number_format($price, 2, '.', ',');
                break;
            case '1 000,00':
                $price = number_format($price, 2, ',', ' ');
                break;
            case '1 000.00':
                $price = number_format($price, 2, '.', ' ');
                break;
        }

        return $this->currency->prefix . $price . $this->currency->suffix;
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
