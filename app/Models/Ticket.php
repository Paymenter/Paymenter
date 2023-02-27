<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ticket extends Model
{
    use HasFactory;

    protected $table = 'tickets';

    protected $fillable = [
        'title',
        'description',
        'status',
        'client',
        'priority',
        'order_id',
    ];

    protected $hidden = [
        'client',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'id', 'order_id');
    }

    public function client()
    {
        return $this->hasOne(User::class, 'id', 'client');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class, 'ticket_id');
    }
}
