<?php

namespace App\Models;

use App\Classes\Price;
use App\Observers\ServiceUpgradeObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([ServiceUpgradeObserver::class])]
class ServiceUpgrade extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_COMPLETED = 'completed';

    public $guarded = [];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function configs()
    {
        return $this->morphMany(ServiceConfig::class, 'configurable');
    }

    public function calculateProratedAmount($oldItem, $newItem): Price
    {
        // Calculate the total number of days in the billing period
        $billingPeriodDays = match ($this->service->plan->billing_unit) {
            'day' => $this->service->plan->billing_period,
            'week' => $this->service->plan->billing_period * 7,
            'month' => $this->service->plan->billing_period * 30,
            'year' => $this->service->plan->billing_period * 365,
            default => 0,
        };

        // Calculate the remaining days until the service expires
        if ($this->service->expires_at) {
            $expiresAt = $this->service->expires_at->copy()->startOfDay();
            $now = Carbon::now()->startOfDay();
            $remainingDays = $expiresAt->diffInDays($now, true);
            $remainingDays = min($remainingDays, $billingPeriodDays);

            // Calculate the prorated amount
            $newPrice = $newItem->price(null, $this->service->plan->billing_period, $this->service->plan->billing_unit, $this->service->currency_code);
            if (empty($oldItem)) {
                $oldPrice = 0;
            } else {
                $oldPrice = $oldItem->price(null, $this->service->plan->billing_period, $this->service->plan->billing_unit, $this->service->currency_code)->price;
            }
            $priceDifference = $newPrice->price - $oldPrice;
            $total = ($priceDifference / $billingPeriodDays) * $remainingDays;
        } else {
            $total = $newItem->price(null, $this->service->plan->billing_period, $this->service->plan->billing_unit, $this->service->currency_code)->price;
        }

        return new Price([
            'price' => $total,
            'currency' => $this->service->currency,
        ]);
    }

    public function calculatePrice(): Price
    {
        $total = $this->calculateProratedAmount(
            $this->service->product,
            $this->product
        )->price;

        foreach ($this->configs as $config) {
            $configValue = $config->configValue;
            if (!$configValue) {
                continue;
            }

            $oldPrice = $this->service->configs->where('config_option_id', $config->config_option_id)->first();

            $ctotal = $this->calculateProratedAmount(
                $oldPrice ? $oldPrice->configValue : null,
                $configValue
            );
            $total += $ctotal->price;
        }

        return new Price([
            'price' => $total,
            'currency' => $currency ?? $this->service->currency,
        ]);
    }
}
