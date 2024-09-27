<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'subject',
        'body',
        'cc',
        'bcc',
    ];

    protected $casts = [
        'cc' => 'array',
        'bcc' => 'array',
    ];
}
