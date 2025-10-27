<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAuthenticationLog extends Model
{
    protected $fillable = [
        'user_id',
        'ip_address',
    ];

    /**
     * Get the user that owns the authentication log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
