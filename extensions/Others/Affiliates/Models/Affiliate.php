<?php

namespace Paymenter\Extensions\Others\Affiliates\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'visitors',
        'reward',
        'discount'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(AffiliateReferral::class);
    }

    /**
     * Get the earnings made by this affiliate
     *
     * @return string
     */
    public function earnings(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $earnings = 0;

                $this->referrals->each(function ($referral) use (&$earnings) {
                    $earnings += $referral->earnings;
                });

                return $earnings;
            },
        );
    }
}
