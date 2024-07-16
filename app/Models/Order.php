<?php

namespace App\Models;

use App\Classes\Price;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'currency_code', 'coupon_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orderProducts()
    {
        return $this->hasMany(OrderProduct::class);
    }

    /**
     * Get the currency corresponding to the order product.
     */
    public function currency()
    {
        return $this->hasOne(Currency::class, 'code', 'currency_code');
    }

    /**
     * Total of the order.
     *
     * @return float
     */
    public function total(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->orderProducts->sum(fn ($orderProduct) => $orderProduct->price * $orderProduct->quantity)
        );
    }

    /**
     * Total of the order.
     *
     * @return string
     */
    public function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->total, 'currency' => $this->currency])
        );
    }

    /**
     * Get all invoices for the order.
     */
    public function invoices(): Attribute
    {
        // Each orderproduct has invoices (it is a hasManyThrough relationship order -> orderProduct -> invoiceItem -> invoice)
        $invoicesId = $this->orderProducts->map(fn ($orderProduct) => $orderProduct->invoiceItems->map(fn ($invoiceItem) => $invoiceItem->invoice_id))->flatten();

        return new Attribute(
            get: fn () => Invoice::whereIn('id', $invoicesId)
        );
    }
}
