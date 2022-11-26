<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'products',
        'expiry_date',
        'status',
        'client',
        'total'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client', 'id');
    }

    public function products()
    {
        return $this->hasMany(OrderProducts::class, 'order_id', 'id');
    }
}
