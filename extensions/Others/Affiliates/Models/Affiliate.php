<?php

namespace Paymenter\Extensions\Others\Affiliates\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Affiliate extends Model
{
    protected $table = 'ext_affiliates';

    protected $fillable = [
        'user_id',
        'code',
        'visitors',
        'reward',
        'discount',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(AffiliateOrder::class);
    }

    /**
     * Get the earnings made by this affiliate
     *
     * @return string
     */
    public function earnings(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $earnings = [];
                $this->orders->each(function ($order) use (&$earnings) {
                    foreach ($order->earnings as $currency => $total) {
                        if (!isset($earnings[$currency])) {
                            $earnings[$currency] = 0;
                        }
                        $earnings[$currency] += $total;
                    }
                });

                return $earnings;
            },
        );
    }
}
