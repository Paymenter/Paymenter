<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $fillable = [
        'user_id',
        'order_id',
        'status',
        'paid_at',
        'due_date',
        'total',
    ];

    public function setStatusAttribute($value)
    {
        if ($value == 'paid') {
            $this->attributes['paid_at'] = now();
        }
        $this->attributes['status'] = $value;
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function total()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->total;
        }

        return $total;
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }
}
