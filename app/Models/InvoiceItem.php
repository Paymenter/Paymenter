<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceItem extends Model
{
    use HasFactory;
    protected $table = 'invoice_items';
    protected $fillable = [
        'invoice_id',
        'description',
        'total',
        'product_id',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }

    public function getTotalAttribute($value)
    {
        return number_format((float)$value, 2, '.', '');
    }

    public function product()
    {
        return $this->belongsTo(OrderProduct::class, 'product_id', 'id');
    }
}