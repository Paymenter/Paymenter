<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'user_id',
        'coupon_id',
    ];

    public function total()
    {
        $total = 0;
        foreach ($this->products as $product) {
            $product->price = $product->price ?? $product->product()->get()->first()->price($product->billing_cycle);
            $total += $product->price * $product->quantity;
        }

        return $total;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id', 'id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
