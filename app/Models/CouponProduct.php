<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class CouponProduct extends Model implements Auditable
{
    use HasFactory, \App\Models\Traits\Auditable;

    protected $fillable = [
        'coupon_id',
        'product_id',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
