<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tickets extends Model
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
        return $this->hasMany(Orders::class, 'id', 'order_id');
    }

    public function client()
    {
        return $this->hasOne(User::class, 'id', 'client');
    }

    public function messages()
    {
        return $this->hasMany(TicketMessages::class, 'ticket_id');
    }
}
