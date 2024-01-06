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
        return $this->getItemsWithProducts()->total;
    }

    public function upgrade()
    {
        return $this->hasOne(OrderProductUpgrade::class, 'invoice_id', 'id');
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
            if ($item->product) {
                $product = $item->product;
                $order = $product->order;
                $coupon = $order->coupon;
                if ($coupon) {
                    if ($coupon->time == 'onetime') {
                        $invoices = $order->invoices;
                        if ($invoices->first()->id !== $this->id) {
                            $coupon = null;
                        }
                    }
                    if ($coupon && $coupon->status !== 'active') {
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
                                $product->discount = $item->total * $coupon->value / 100;
                            } else {
                                $product->discount = $coupon->value;
                            }
                        }
                    } else {
                        if ($coupon->type == 'percent') {
                            $product->discount = $item->total * $coupon->value / 100;
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
                // $item->price can't be less than 0
                if ($product->price < 0) {
                    $product->price = 0;
                }
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
        $tax = $this->getTax($total);
        if ($tax->amount > 0 && config('settings::tax_type') == 'exclusive') {
            $total += $tax->amount;
        }
        // Return total and products as object
        return (object) [
            'total' => $total,
            'products' => $products,
            'tax' => $tax,
        ];
    }

    public function getTax($total)
    {
        if (!config('settings::tax_enabled')) return new TaxRate();
        $tax = 0;
        if (!auth()->check()) {
            $taxrate = TaxRate::where('country', 'all')->first();
        } else {
            $taxrate = TaxRate::whereIn('country', [auth()->user()->country, 'all'])->get()->sortBy(function ($taxRate) {
                return $taxRate->country == 'all';
            })->first();
        }
        if(!$taxrate) return new TaxRate();
        
        $taxrate->amount = $total * ($taxrate->rate / 100);
        return $taxrate;
    }
}
