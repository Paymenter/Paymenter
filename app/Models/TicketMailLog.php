<?php

namespace App\Models;

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
