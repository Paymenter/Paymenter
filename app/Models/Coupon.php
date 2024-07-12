<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'code',
        'value',
        'max_uses',
        'starts_at',
        'expires_at',

    ];

    public function products()
    {
        return $this->hasMany(CouponProduct::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
