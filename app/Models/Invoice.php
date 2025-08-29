<?php

namespace App\Models;

use App\Classes\PDF;
use App\Classes\Price;
use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([InvoiceObserver::class])]
class Invoice extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = ['number', 'user_id', 'currency_code', 'due_at', 'status'];

    protected $casts = [
        'due_at' => 'date',
    ];

    public bool $send_create_email = true;

    /**
     * Total of the invoice.
     *
     * @return string
     */
    public function total(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items->sum(fn ($item) => $item->price * $item->quantity)
        );
    }

    /**
     * Total of the invoice.
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
     * Formatted remaining amount of the invoice.
     */
    public function formattedRemaining(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->remaining, 'currency' => $this->currency])
        );
    }

    /**
     * Remaining amount of the invoice.
     */
    public function remaining(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total - $this->transactions->sum('amount')
        );
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_code');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(InvoiceTransaction::class);
    }

    public function pdf(): Attribute
    {
        return Attribute::make(
            get: fn () => PDF::generateInvoice($this)
        );
    }
}
