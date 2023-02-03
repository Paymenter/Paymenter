<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoices extends Model
{
    use HasFactory;
    protected $table = 'invoices';
    protected $fillable = [
        'user_id',
        'order_id',
        'status',
        'paid_at',
    ];

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
