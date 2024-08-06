<?php

namespace App\Models;

use App\Classes\Price;
use App\Models\Traits\HasProperties;
use App\Observers\OrderProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([OrderProductObserver::class])]
class OrderProduct extends Model
{
    use HasFactory, HasProperties;

    protected $fillable = [
        'order_id',
        'product_id',
        'plan_id',
        'quantity',
        'price',
        'expires_at',
        'subscription_id',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    /**
     * Get the order that owns the order product.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function currency(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->order->currency
        );
    }

    /**
     * Price of the order product.
     *
     * @return string
     */
    public function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->price * $this->quantity, 'currency' => $this->order->currency])
        );
    }

    /**
     * Get the description for the next invoice item.
     */
    public function description(): Attribute
    {
        $date = $this->expires_at ?? now();
        $endDate = $date->copy()->addDays($this->plan->billing_duration);

        return Attribute::make(
            get: fn () => $this->product->name . ' (' . $date->format('M d, Y') . ' - ' . $endDate->format('M d, Y') . ')'
        );
    }

    /**
     * Get the product corresponding to the order product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the plan corresponding to the order product.
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the order product's configurations.
     */
    public function configs()
    {
        return $this->hasMany(OrderProductConfig::class);
    }

    /**
     * Get invoiceItems
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get invoices
     */
    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, InvoiceItem::class, 'order_product_id', 'id', 'id', 'invoice_id');
    }
}
