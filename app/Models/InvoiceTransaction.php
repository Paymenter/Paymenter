<?php

namespace App\Models;

use App\Classes\Price;
use App\Observers\InvoiceTransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([InvoiceTransactionObserver::class])]
class InvoiceTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'gateway_id',
        'amount',
        'fee',
        'transaction_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    /**
     * Formatted remaining amount of the invoice.
     */
    public function formattedFee(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->fee, 'currency' => $this->invoice->currency])
        );
    }

    /**
     * Formatted remaining amount of the invoice.
     */
    public function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->amount, 'currency' => $this->invoice->currency])
        );
    }
}
