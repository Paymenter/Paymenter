<?php

namespace Paymenter\Extensions\Others\Affiliates\Models;

use App\Helpers\ExtensionHelper;
use App\Models\Order;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AffiliateOrder extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'affiliate_id'
    ];

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the earnings made from this order
     *
     * @return string
     */
    public function earnings(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $earnings = [];

                /** @var Collection */
                $invoices = $this->order->invoices;
                $extension = ExtensionHelper::getExtension('other', 'Affiliates');
                $reward_percentage = $this->affiliate->reward ?: $extension->config('default_reward');

                $invoices->each(function ($invoice) use (&$earnings, $reward_percentage) {
                    if ($invoice->status !== 'paid') return;
                    if (!isset($earnings[$invoice->currency_code])) $earnings[$invoice->currency_code] = 0;
                    $earnings[$invoice->currency_code] += $invoice->total * $reward_percentage / 100;
                });

                foreach ($earnings as $currency => $total) {
                    // Round to 2 decimal places
                    $earnings[$currency] = round($total, 2);
                }

                return $earnings;
            },
        );
    }
}
