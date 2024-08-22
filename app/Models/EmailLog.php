<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_template_id',
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
