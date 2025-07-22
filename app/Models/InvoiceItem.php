<?php

namespace App\Models;

use App\Classes\Price;
use App\Observers\InvoiceItemObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([InvoiceItemObserver::class])]
class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'service_id',
        'quantity',
        'price',
        'description',
        'gateway_id',
        'reference_id',
        'reference_type',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function total()
    {
        return $this->price * $this->quantity;
    }

    public function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->total(), 'currency' => $this->invoice->currency])
        );
    }

    public function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->price, 'currency' => $this->invoice->currency])
        );
    }
}
