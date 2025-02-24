<?php

namespace App\Models;

use App\Classes\Price;
use App\Observers\ServiceUpgradeObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ServiceUpgradeObserver::class])]
class ServiceUpgrade extends Model
{
    use HasFactory;

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

    public function calculatePrice()
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
        $expiresAt = $this->service->expires_at->copy()->startOfDay();
        $now = Carbon::now()->startOfDay();
        $remainingDays = $expiresAt->diffInDays($now, true);

        // Calculate the prorated amount
        $newPrice = $this->product->price(null, $this->service->plan->billing_period, $this->service->plan->billing_unit, $this->service->order->currency_code);
        $priceDifference = $newPrice->price - $this->service->price;
        $total = ($priceDifference / $billingPeriodDays) * $remainingDays;

        return new Price([
            'price' => $total,
            'currency' => $this->service->order->currency,
        ]);
    }
}
