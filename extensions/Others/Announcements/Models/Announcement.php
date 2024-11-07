<?php

namespace Paymenter\Extensions\Others\Announcements\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'ext_announcements';

    protected $fillable = [
        'title',
        'content',
        'description',
        'published_at',
        'is_active',
        'slug',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
