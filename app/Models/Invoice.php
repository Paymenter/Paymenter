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
        'paid_with',
        // Reference is used to store the transaction ID from the payment gateway
        'paid_reference',
        'due_date',
    ];

    protected $hidden = [
        'credits',
    ];

    public function setStatusAttribute($value)
    {
        if ($value == 'paid') {
            $this->attributes['paid_at'] = now();
        }
        $this->attributes['status'] = $value;
    }

    public function isPaid()
    {
        return $this->status == 'paid';
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
        $total = 0.00;
        foreach ($this->items as $item) {
            $product = $item->product()->get()->first() ?? null;
            if ($product) {
                $order = $product->order()->get()->first() ?? null;
            } else {
                $order = null;
            }
            if ($order == null) {
                $total = InvoiceItem::where('invoice_id', $this->id)->sum('total');
                return number_format($total, 2, '.', '');
            }
            $coupon = $order->coupon()->get()->first();
            if ($coupon) {
                $couponStatus = true;
                if ($coupon->time == 'onetime') {
                    $invoices = $order->invoices;
                    if ($invoices->first()->id == $this->id) {
                        $coupon = $order->coupon()->get()->first();
                    } else {
                        $coupon = null;
                    }
                }

                if (!$couponStatus) {
                    $coupon = NULL;
                }
            }
            $productId = $product->product;
            if ($coupon) {
                if (!empty($coupon->products)) {
                    if (!in_array($productId->id, $coupon->products)) {
                        $product->discount = 0;
                    } else {
                        if ($coupon->type == 'percent') {
                            $product->discount = $product->price * $coupon->value / 100;
                        } else {
                            $product->discount = $coupon->value;
                        }
                    }
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
            $product->price = $item->total - $product->discount;
            $total += number_format((float)$product->price, 2, '.', '');
        }

        // Return 2 decimal places
        return number_format($total, 2, '.', '');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function getItemsWithProducts()
    {
        $products = [];
        $total = 0;
        foreach ($this->items as $item) {
            if ($item->product_id) {
                $product = $item->product;
                $order = $product->order;
                $coupon = $order->coupon()->get()->first();
                if ($coupon) {
                    if ($coupon->time == 'onetime') {
                        $invoices = $order->invoices;
                        if ($invoices->first()->id == $this->id) {
                            $coupon = $order->coupon()->get()->first();
                        } else {
                            $coupon = null;
                        }
                    }
                    if ($coupon && $coupon->status !== 'active'){
                        $coupon = null;
                    }
                    if ($coupon && $coupon->end_at && $coupon->end_at < now()) {
                        $coupon = null;
                    }
                    if ($coupon && $coupon->start_at && $coupon->start_at > now()) {
                        $coupon = null;
                    }
                }
                $productId = $product->product;
                if ($coupon) {
                    if (!empty($coupon->products)) {
                        if (!in_array($productId->id, $coupon->products)) {
                            $product->discount = 0;
                        } else {
                            if ($coupon->type == 'percent') {
                                $product->discount = $product->price * $coupon->value / 100;
                            } else {
                                $product->discount = $coupon->value;
                            }
                        }
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
                $product->original_price = $item->total;
                $product->price = $item->total - $product->discount;
                $products[] = $product;
                $total += $product->price;
            } else {
                $product = $item;
                $product->price = $item->total;
                $product->name = $item->description;
                $product->discount = 0;
                $product->quantity = 1;
                $products[] = $product;
                $total += ($product->price - $product->discount);
            }
        }
        // Return total and products as object
        return (object) [
            'total' => $total,
            'products' => $products
        ];
    }
}
