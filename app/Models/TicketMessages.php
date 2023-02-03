<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketMessages extends Model
{
    use HasFactory;
    protected $table = 'ticketMessages';
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
