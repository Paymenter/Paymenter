<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketMessages extends Model
{
    use HasFactory;
    protected $table = 'ticketMessages';
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message'
    ];

    function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
