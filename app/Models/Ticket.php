<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'status',
        'priority',
        'department',
        'user_id',
        'assigned_to',
        'order_product_id',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }
}
