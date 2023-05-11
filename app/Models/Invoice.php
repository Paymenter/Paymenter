<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $fillable = [
        'user_id',
        'order_id',
        'status',
        'paid_at',
        'due_date',
        'total',
    ];

    public function setStatusAttribute($value)
    {
        if ($value == 'paid') {
            $this->attributes['paid_at'] = now();
        }
        $this->attributes['status'] = $value;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function total()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->total;
        }

        return $total;
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function getItemsWithProducts(){
        $products = [];
        $total = 0;
        foreach ($this->items as $item) {
            if ($item->product_id) {
                $product = $item->product()->get()->first();
                $order = $product->order()->get()->first();
                $coupon = $order->coupon()->get()->first();
                if ($coupon) {
                    if ($coupon->time == 'onetime') {
                        $invoices = $order->invoices()->get();
                        if ($invoices->count() == 1) {
                            $coupon = $order->coupon()->get()->first();
                        } else {
                            $coupon = null;
                        }
                    }
                }

                if ($coupon) {
                    if (!in_array($product->id, $coupon->products) && !empty($coupon->products)) {
                        $product->discount = 0;
                    } else {
                        if ($coupon->type == 'percent') {
                            $product->discount = $product->price * $coupon->value / 100;
                        } else {
                            $product->discount = $coupon->value;
                        }
                    }
                } else {
                    $product->discount = 0;
                }
                $product->name = $item->description;
                $product->price = $item->total;
                $products[] = $product;
                $total += ($product->price - $product->discount) * $product->quantity;
            } else {
                $product = $item;
                $product->price = $item->total;
                $product->name = $item->description;
                $product->discount = 0;
                $product->quantity = 1;
                $products[] = $product;
                $total += ($product->price - $product->discount) * $product->quantity;
            }
        }
        // Return total and products as object
        return (object) [
            'total' => $total,
            'products' => $products
        ];
    }
}
