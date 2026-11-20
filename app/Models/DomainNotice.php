<?php

namespace App\Models;

class DomainNotice extends Model
{
    protected $fillable = [
        'domain_id',
        'key',
        'stage',
        'cycle',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }
}
