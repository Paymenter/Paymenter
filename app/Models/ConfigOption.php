<?php

namespace App\Models;

use App\Classes\Price as PriceClass;
use App\Models\Traits\HasPlans;
use Closure;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class ConfigOption extends Model implements Auditable
{
    use HasFactory, HasPlans, Traits\Auditable;

    protected $dontShowUnavailablePrice = true;

    protected $fillable = [
        'name',
        'description',
        'env_variable',
        'type',
        'sort',
        'hidden',
        'parent_id',
        'upgradable',
        'min',
        'max',
        'step',
        'show_as_slider',
    ];

    protected $casts = [
        'min' => 'float',
        'max' => 'float',
        'step' => 'float',
        'show_as_slider' => 'boolean',
    ];

    /**
     * Get the parent option.
     */
    public function parent()
    {
        return $this->belongsTo(ConfigOption::class, 'parent_id');
    }

    /**
     * Get the options that belong to the parent. (children or options)
     */
    public function children()
    {
        return $this->hasMany(ConfigOption::class, 'parent_id')->orderBy('sort');
    }

    /**
     * Get the products that belong to the option.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'config_option_products');
    }

    /**
     * Get the service configs that belong to the option.
     */
    public function serviceConfigs()
    {
        return $this->hasMany(ServiceConfig::class, 'config_option_id');
    }

    /**
     * A number option is priced per unit as soon as it has a child holding the unit price.
     */
    public function hasUnitPricing(): bool
    {
        return $this->type === 'number' && $this->children->isNotEmpty();
    }

    /**
     * A slider needs both ends of the range to know where to start and stop.
     */
    public function hasSlider(): bool
    {
        return $this->type === 'number' && $this->show_as_slider && $this->min !== null && $this->max !== null;
    }

    /**
     * Rules for a number option: the amount has to stay within the configured range and follow its increment.
     */
    public function numberValidationRules(): array
    {
        $rules = ['required', 'numeric'];

        if ($this->min !== null) {
            $rules[] = 'min:' . $this->min;
        }
        if ($this->max !== null) {
            $rules[] = 'max:' . $this->max;
        }
        if ($this->step) {
            $rules[] = function (string $attribute, $value, Closure $fail) {
                $steps = ((float) $value - ($this->min ?? 0)) / $this->step;

                if (abs($steps - round($steps)) > 0.0000001) {
                    $fail(__('The :attribute must be in increments of :step.', ['step' => $this->step + 0]));
                }
            };
        }

        return $rules;
    }

    /**
     * The price of a single unit of a number option.
     */
    public function unitPrice($billing_period = null, $billing_unit = null, $currency = null): ?PriceClass
    {
        return $this->children->first()?->price(null, $billing_period, $billing_unit, $currency);
    }

    /**
     * The price a number option adds for the entered quantity: the optional base price of the
     * option itself plus its unit price for every entered unit.
     *
     * @return object{price: float, setup_fee: float, available: bool}
     */
    public function priceForQuantity($quantity, $billing_period = null, $billing_unit = null, $currency = null): object
    {
        $quantity = max(0, (float) $quantity);
        $total = 0;
        $setupFee = 0;
        $available = true;

        if ($unitPrice = $this->unitPrice($billing_period, $billing_unit, $currency)) {
            $available = $available && $unitPrice->available;
            $total += $unitPrice->price * $quantity;
            $setupFee += ($unitPrice->setup_fee ?? 0) * $quantity;
        }

        // The base price is optional: only options that got their own plans charge one.
        if ($this->plans->isNotEmpty()) {
            $basePrice = $this->price(null, $billing_period, $billing_unit, $currency);
            $available = $available && $basePrice->available;
            $total += $basePrice->price;
            $setupFee += $basePrice->setup_fee ?? 0;
        }

        return (object) [
            'price' => $total,
            'setup_fee' => $setupFee,
            'available' => $available,
        ];
    }
}
