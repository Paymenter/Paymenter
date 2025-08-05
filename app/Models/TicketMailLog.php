<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMailLog extends Model
{
    protected $fillable = [
        'message_id',
        'subject',
        'from',
        'to',
        'body',
        'status',
    ];
}
