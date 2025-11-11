<?php

namespace App\Models;

use App\Observers\CartObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(CartObserver::class)]
class Cart extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'currency_code',
        'coupon_id',
    ];

    public function uniqueIds()
    {
        return [
            'ulid',
        ];
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
