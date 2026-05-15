<?php

namespace App\Models;

use App\Classes\Price;
use App\Enums\CreditNoteType;
use App\Observers\CreditNoteObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([CreditNoteObserver::class])]
class CreditNote extends Model implements Auditable
{
    use HasFactory, Traits\Auditable;

    protected $fillable = [
        'invoice_id',
        'type',
        'number',
        'amount',
        'description',
        'is_admin_only',
        'trigger_type',
    ];

    protected $casts = [
        'type' => CreditNoteType::class,
        'amount' => 'decimal:2',
        'is_admin_only' => 'boolean',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Formatted amount of the credit note.
     */
    public function formattedAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => new Price(['price' => $this->amount, 'currency' => $this->invoice->currency])
        );
    }
}
