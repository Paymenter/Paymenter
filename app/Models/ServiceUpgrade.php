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
        if (
            !$newItem ||
            ($oldItem && (
                (method_exists($newItem, 'is') && $newItem->is($oldItem)) ||
                (isset($oldItem->id, $newItem->id) && $oldItem->id === $newItem->id)
            ))
        ) {
            return $this->makePrice();
        }

        $plan = $this->service->plan;
        $newPrice = $newItem->price(null, $plan->billing_period, $plan->billing_unit, $this->service->currency_code)->price;

        if (!$this->service->expires_at) {
            return $this->makePrice($newPrice);
        }

        $billingPeriodDays = $this->getBillingPeriodDays();
        $remainingDays = $this->getRemainingDays();
        $priceDifference = $newPrice - $this->resolveOldItemPrice($oldItem);
        $total = $billingPeriodDays > 0 ? ($priceDifference / $billingPeriodDays) * $remainingDays : $priceDifference;

        return $this->makePrice($total);
    }

    public function calculatePrice(): Price
    {
        $total = $this->calculateProratedAmount($this->service->product, $this->product)->price;

        foreach ($this->configs as $config) {
            if ($configValue = $config->configValue) {
                $oldPrice = $this->service->configs->where('config_option_id', $config->config_option_id)->first();
                $total += $this->calculateProratedAmount($oldPrice?->configValue, $configValue)->price;
            }
        }

        // Cap refunds to what was actually paid when coupon exists
        if ($total < 0 && $this->service->coupon_id) {
            $total = max($total, -$this->getMaxRefundAmount());
        }

        return $this->makePrice($total);
    }

    protected function resolveOldItemPrice($oldItem): float
    {
        if (empty($oldItem)) {
            return 0;
        }

        $price = $oldItem->price(
            null,
            $this->service->plan->billing_period,
            $this->service->plan->billing_unit,
            $this->service->currency_code
        )->price ?? 0;

        return (float) $price;
    }

    protected function makePrice(float $amount = 0): Price
    {
        return new Price([
            'price' => $amount,
            'currency' => $this->service->currency,
        ]);
    }

    protected function getBillingPeriodDays(): int
    {
        $plan = $this->service->plan;

        return match ($plan->billing_unit) {
            'day' => $plan->billing_period,
            'week' => $plan->billing_period * 7,
            'month' => $plan->billing_period * 30,
            'year' => $plan->billing_period * 365,
            default => 0,
        };
    }

    protected function getRemainingDays(): int
    {
        if (!$this->service->expires_at) {
            return 0;
        }
        $billingPeriodDays = $this->getBillingPeriodDays();

        return min($this->service->expires_at->copy()->startOfDay()->diffInDays(Carbon::now()->startOfDay(), true), $billingPeriodDays);
    }

    public function getMaxRefundAmount(): float
    {
        // We don't refund if service has no due date (one-time or free plans)
        if (!$this->service->expires_at) {
            return 0;
        }
        $billingPeriodDays = $this->getBillingPeriodDays();
        $remainingDays = $this->getRemainingDays();
        $paidAmount = (float) $this->service->calculatePrice();

        return $billingPeriodDays > 0 ? ($paidAmount / $billingPeriodDays) * $remainingDays : $paidAmount;
    }
}
