<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'products',
        'expiry_date',
        'status',
        'client',
        'total',
        'coupon',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client', 'id');
    }

    public function products()
    {
        return $this->hasMany(OrderProducts::class, 'order_id', 'id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoices::class, 'order_id', 'id');
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon', 'id');
    }
}
