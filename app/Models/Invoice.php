<?php

namespace App\Models;

use App\Classes\PDF;
use App\Classes\Price;
use App\Classes\Settings;
use App\Enums\AdjustmentNoteType;
use App\Enums\InvoiceTransactionStatus;
use App\Models\Traits\HasProperties;
use App\Observers\InvoiceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([InvoiceObserver::class])]
class Invoice extends Model implements Auditable
{
    use HasFactory, HasProperties, Traits\Auditable;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PAID = 'paid';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = ['number', 'user_id', 'currency_code', 'due_at', 'status', 'cancellation_reason'];

    protected $casts = [
        'due_at' => 'date',
    ];

    public bool $send_create_email = true;

    public function createCancellationCreditNote($description = null): void {
        $this->adjustmentNotes()->create([
            'type' => AdjustmentNoteType::Credit->value,
            'amount' => -1 * abs($this->total),
            'description' => $description ?? 'Automatic credit note generated after overdue invoice cancellation.',
            'is_admin_only' => true,
        ]);
    }
    /**
     * Total of the invoice.
     *
     * @return string
     */
    public function total(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items->sum(fn ($item) => $item->price * $item->quantity)
                + $this->adjustmentNotes->where('type', AdjustmentNoteType::Debit->value)->sum('amount')
                + $this->adjustmentNotes->where('type', AdjustmentNoteType::Credit->value)->sum('amount')
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
            get: fn () => new Price(['price' => $this->total, 'currency' => $this->currency, 'tax' => $this->tax])
        );
    }

    /**
     * Current balance of the invoice, accounting for total + debit notes - credit notes - succeeded transactions (net of refunds).
     */
    public function currentBalance(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total
                - $this->transactions->where('status', InvoiceTransactionStatus::Succeeded)->sum(function ($txn) {
                    return $txn->amount - $txn->refunded_amount;
                })
        );
    }

    /**
     * Formatted current balance of the invoice.
     */
    public function formattedCurrentBalance(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->currentBalance, 'currency' => $this->currency, 'tax' => $this->tax])
        );
    }

    /**
     * Formatted remaining amount of the invoice.
     */
    public function formattedRemaining(): Attribute
    {
        return Attribute::make(
            get: fn () => new Price(['price' => $this->remaining, 'currency' => $this->currency, 'tax' => $this->tax])
        );
    }

    /**
     * Remaining amount of the invoice, net of refunded amounts.
     */
    public function remaining(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->total - $this->transactions->where('status', InvoiceTransactionStatus::Succeeded)->sum(function ($txn) {
                return $txn->amount - $txn->refunded_amount;
            })
        );
    }

    /**
     * Get tax model of the invoice.
     * Either via locked invoice or via users country.
     */
    public function tax(): Attribute
    {
        if (config('settings.invoice_snapshot', true) && $this?->snapshot?->tax_name) {
            return Attribute::make(
                get: fn () => new TaxRate([
                    'name' => $this->snapshot->tax_name,
                    'rate' => $this->snapshot->tax_rate,
                    'country' => $this->snapshot->tax_country,
                ])
            );
        }
        return Attribute::make(
            get: fn () => Settings::tax($this->user)
        );
    }

    public function userProperties(): Attribute
    {
        if (config('settings.invoice_snapshot', true) && $this?->snapshot?->properties) {
            return Attribute::make(
                get: fn () => $this->snapshot->properties
            );
        }

        return Attribute::make(
            get: fn () => $this->user->properties()->with('parent_property')->whereHas('parent_property', function ($query) {
                $query->where('show_on_invoice', true);
            })->pluck('value', 'key')->toArray()
        );
    }

    public function userName(): Attribute
    {
        if (config('settings.invoice_snapshot', true) && $this?->snapshot?->name) {
            return Attribute::make(
                get: fn () => $this->snapshot->name
            );
        }

        return Attribute::make(
            get: fn () => $this->user->name
        );
    }

    public function billTo(): Attribute
    {
        if (config('settings.invoice_snapshot', true) && $this?->snapshot?->bill_to) {
            return Attribute::make(
                get: fn () => $this->snapshot->bill_to
            );
        }

        return Attribute::make(
            get: fn () => config('settings.bill_to_text', config('settings.company_name'))
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

    public function adjustmentNotes()
    {
        return $this->hasMany(AdjustmentNote::class);
    }

    public function snapshot()
    {
        return $this->hasOne(InvoiceSnapshot::class);
    }

    public function pdf(): Attribute
    {
        return Attribute::make(
            get: fn () => PDF::generateInvoice($this)
        );
    }

    public function getRouteKey()
    {
        // Prefer using number if it’s set, otherwise fallback to id
        return $this->number ?: $this->id;
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if ($field) {
            return $this->where($field, $value)->firstOrFail();
        }

        // Try to find by number first
        $query = $this->where('number', $value);

        // Only try to match by ID if value is numeric
        if (is_numeric($value)) {
            $query->orWhere('id', $value);
        }

        return $query->orderByRaw('(number = ?) DESC', [$value])->firstOrFail();
    }
}
