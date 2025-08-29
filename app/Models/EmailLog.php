<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subject',
        'body',
        'to',
        'sent_at',
        'status',
        'error',
        'job_uuid',
    ];
}
