<?php

namespace App\Classes;

use App\Models\TaxRate;

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

    public function hasDiscount(): bool
    {
        return $this->discount > 0;
    }

    public function __construct($priceAndCurrency = null, $free = false, $dontShowUnavailablePrice = false, $apply_exclusive_tax = false, TaxRate|int|null $tax = null)
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
                'total' => $this->format($this->total),
                'total_tax' => $this->format($this->total_tax),
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
        if (config('settings.tax_enabled', false)) {
            $tax ??= Settings::tax();
            if ($tax) {
                // Inclusive has the tax included in the price
                if (config('settings.tax_type', 'inclusive') == 'inclusive' || !$apply_exclusive_tax) {
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
            'total' => $this->format($this->total),
            'price' => $this->format($this->price),
            'setup_fee' => $this->format($this->setup_fee),
            'tax' => $this->format($this->tax),
            'setup_fee_tax' => $this->format($this->setup_fee_tax),
            'total_tax' => $this->format($this->total_tax),
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
        $price = $price ?? 0;
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
        return $this->formatted->total;
    }

    public function __get($name)
    {
        return match ($name) {
            'total' => number_format($this->price + ($this->setup_fee ?? 0), 2, '.', ''),
            'total_tax' => number_format($this->tax + $this->setup_fee_tax, 2, '.', ''),
            // Subtotal is price + setup_fee - tax - setup_fee_tax
            'subtotal' => number_format(($this->price + ($this->setup_fee ?? 0)) - ($this->tax + $this->setup_fee_tax), 2, '.', ''),
            'available' => $this->currency || $this->is_free ? true : false,
            default => $this->$name ?? null,
        };
    }
}
