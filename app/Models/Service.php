<?php

namespace App\Models;

use App\Classes\Price;
use App\Classes\Settings;
use App\Models\Traits\HasProperties;
use App\Observers\ServiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Log;

#[ObservedBy([ServiceObserver::class])]
class Service extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory, HasProperties;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'order_id',
        'product_id',
        'plan_id',
        'quantity',
        'price',
        'expires_at',
        'subscription_id',
        'status',
        'coupon_id',
        'user_id',
        'currency_code',
        'billing_agreement_id',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    /**
     * Get the order that owns the service.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the coupon that owns the service.
     */
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the currency corresponding to the service.
     */
    public function currency()
    {
        return $this->hasOne(Currency::class, 'code', 'currency_code');
    }

    /**
     * Get the user that owns the service.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Price of the service.
     *
     * @return string
     */
    public function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->price * $this->quantity, 'currency' => $this->currency])
        );
    }

    /**
     * Get the description for the next invoice item.
     */
    public function description(): Attribute
    {
        if ($this->plan->type == 'free' || $this->plan->type == 'one-time') {
            return Attribute::make(
                get: fn () => $this->product->name
            );
        }
        $date = $this->expires_at ?? now();
        $endDate = $date->copy()->{'add' . ucfirst($this->plan->billing_unit) . 's'}($this->plan->billing_period);

        return Attribute::make(
            get: fn () => $this->product->name . ' (' . $date->format('M d, Y') . ' - ' . $endDate->format('M d, Y') . ')'
        );
    }

    /**
     * Calculate next due date.
     */
    public function calculateNextDueDate()
    {
        if ($this->plan->type == 'one-time' || $this->plan->type == 'free') {
            return null;
        }
        if (!$this->expires_at || $this->status != self::STATUS_ACTIVE) {
            // Make sure that if a service is being renewed after suspension or pending, we use the current date as base
            $date = now();
        } else {
            $date = $this->expires_at;
        }

        return $date->{'add' . ucfirst($this->plan->billing_unit) . 's'}($this->plan->billing_period);
    }

    /**
     * Get the product corresponding to the service.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the plan corresponding to the service.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the service's configurations.
     */
    public function configs()
    {
        return $this->morphMany(ServiceConfig::class, 'configurable');
    }

    /**
     * Get invoiceItems
     */
    public function invoiceItems()
    {
        return $this->morphMany(InvoiceItem::class, 'reference');
    }

    /**
     * Get invoices
     */
    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, InvoiceItem::class, 'reference_id', 'id', 'id', 'invoice_id')->where('reference_type', Service::class);
    }

    /**
     * Get cancellation requests
     */
    public function cancellation()
    {
        return $this->hasOne(ServiceCancellation::class);
    }

    public function cancellable(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status !== 'cancelled' && $this->plan->type != 'free' && $this->plan->type != 'one-time' && !$this->cancellation?->exists()
        );
    }

    public function upgradable(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->productUpgrades()->count() > 0 || $this->product->upgradableConfigOptions()->count() > 0) && $this->status == 'active' && $this->upgrade->where('status', ServiceUpgrade::STATUS_PENDING)->count() == 0
        );
    }

    public function productUpgrades()
    {
        return $this->product->upgrades->filter(function ($product) {
            // Check stock
            if ($product->stock !== null && ($product->stock - $this->quantity) < 0) {
                return null;
            }
            $plan = $product->plans()->where('billing_unit', $this->plan->billing_unit)->where('billing_period', $this->plan->billing_period)->get();
            // Only get the upgrades that have the exact same billing cycle as the service
            if ($plan->count() > 0) {
                $product->plan = $plan->first();

                return $product;
            }

            return null;
        });
    }

    public function calculatePrice()
    {
        // Calculate the price based on the plan and config options
        $price = $this->plan->price()->price;

        // Resolve effective slider value once per dynamic_slider config: prefer
        // the migrated slider_value, fall back to the legacy property bag (set
        // by the dual-write path before the backfill artisan command runs).
        $propertyValues = $this->properties()->pluck('value', 'key');

        $resolvedSliderValues = $this->configs->mapWithKeys(function ($config) use ($propertyValues) {
            $configOption = $config->configOption;
            if (! $configOption || $configOption->type !== 'dynamic_slider') {
                return [];
            }

            $sliderValue = $config->slider_value;

            if ($sliderValue === null) {
                $propertyKey   = $configOption->env_variable ?: $configOption->name;
                $propertyValue = $propertyValues->get($propertyKey);
                if ($propertyValue !== null && is_numeric($propertyValue)) {
                    $sliderValue = (float) $propertyValue;
                }
            }

            return $sliderValue !== null ? [$config->id => (float) $sliderValue] : [];
        });

        // Add shared dynamic_slider base price once per product, but only when at
        // least one slider value can be resolved. This avoids charging the base
        // alone during the backfill window when slider_value is null and no legacy
        // property is available — which would otherwise under/overcharge customers.
        if ($resolvedSliderValues->isNotEmpty()) {
            $price += $this->plan->dynamicSliderBasePrice();
        }

        $this->configs->each(function ($config) use (&$price, $propertyValues, $resolvedSliderValues) {
            $configOption = $config->configOption;

            // Handle dynamic_slider configs (priced via slider_value or migrated property).
            if ($configOption && $configOption->type === 'dynamic_slider') {
                $sliderValue = $resolvedSliderValues->get($config->id);

                if ($sliderValue !== null) {
                    // Read-time consistency check: warn when the migrated property
                    // diverges from a stored slider_value. Only meaningful once
                    // slider_value is set; during the backfill window the property
                    // is the only source so no divergence is possible.
                    if ($config->slider_value !== null) {
                        $propertyKey   = $configOption->env_variable ?: $configOption->name;
                        $propertyValue = $propertyValues->get($propertyKey);
                        if ($propertyValue !== null && abs((float) $propertyValue - (float) $sliderValue) > 1e-6) {
                            Log::warning('dynamic_slider value divergence detected', [
                                'service_id'       => $this->id,
                                'config_option_id' => $configOption->id,
                                'property_value'   => $propertyValue,
                                'slider_value'     => $config->slider_value,
                            ]);
                        }
                    }

                    $price += $configOption->calculateDynamicPriceDelta(
                        $sliderValue,
                        $this->plan->billing_period,
                        $this->plan->billing_unit
                    );
                }

                return;
            }

            $configValue = $config->configValue;
            if ($configValue) {
                $price += $configValue->price(null, $this->plan->billing_period, $this->plan->billing_unit, $this->currency_code)->price;
            }
        });

        // Add coupon discount if applicable
        if ($this->coupon) {
            $invoices = $this->invoices()->where('status', 'paid')->count() + 1;
            // If it already used for the recurring period, do not apply the discount
            if ($this->coupon->recurring == 0 || $invoices <= $this->coupon->recurring) {
                $discount = $this->coupon->calculateDiscount($price);
                $price -= $discount;
            }
        }

        $price = (new Price([
            'price' => $price,
            'currency' => $this->currency,
        ], apply_exclusive_tax: true, tax: Settings::tax($this->user)))->price;

        return number_format($price, 2, '.', '');
    }

    public function upgrade()
    {
        return $this->hasMany(ServiceUpgrade::class);
    }

    public function billingAgreement()
    {
        return $this->belongsTo(BillingAgreement::class, 'billing_agreement_id');
    }
}
