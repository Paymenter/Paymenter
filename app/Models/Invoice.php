<?php

namespace App\Models;

use App\Classes\Price;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'currency_code', 'issued_at', 'due_at'];

    protected $casts = [
        'issued_at' => 'date',
        'due_at' => 'date',
    ];

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
}
