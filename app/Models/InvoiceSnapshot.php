<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSnapshot extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceSnapshotFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'properties',
        'tax_name',
        'tax_rate',
        'tax_country',
        'bill_to',
        'invoice_id',
    ];

    protected $casts = [
        'properties' => 'array',
        'tax_rate' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
