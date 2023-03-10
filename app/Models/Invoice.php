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
}
