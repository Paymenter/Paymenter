<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderProduct extends Model
{
    use HasFactory;
    protected $table = 'order_products';
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'billing_cycle',
        'expiry_date',
        'status',
    ];

    public function config()
    {
        return $this->hasMany(OrderProductConfig::class, 'order_product_id', 'id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function invoices()
    {
        return $this->hasOne(InvoiceItem::class, 'product_id', 'id')->get()->first() ? $this->hasOne(InvoiceItem::class, 'product_id', 'id')->get()->first()->invoice() : new Invoice();
    }

    public function getInvoices()
    {
        return $this->hasMany(InvoiceItem::class, 'product_id', 'id')->get()->map(function ($invoiceItem) {
            return $invoiceItem->invoice()->get()->first();
        });
    }
    
    public function getOpenInvoices()
    {
        return $this->getInvoices()->filter(function ($invoice) {
            if ($invoice->total() == 0)
                return false;
            return $invoice->status == 'pending';
        });
    }
}
