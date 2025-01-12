<?php

namespace Paymenter\Extensions\Others\Affiliates\Models;

use App\Helpers\ExtensionHelper;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class AffiliateReferral extends Model
{
    protected $fillable = [
        'user_id',
        'affiliate_id'
    ];

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    /**
     * Get the earnings made from this referral user
     *
     * @return string
     */
    public function earnings(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $earnings = [];

                /** @var Collection */
                $invoices = $this->referredUser->invoices;
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
