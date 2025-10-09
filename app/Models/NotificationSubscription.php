<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'endpoint',
        'p256dh_key',
        'auth_key',
    ];

    protected $casts = [
        'p256dh_key' => 'encrypted',
        'auth_key' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return \Minishlink\WebPush\Subscription::create([
            'endpoint' => $this->endpoint,
            'publicKey' => $this->p256dh_key,
            'authToken' => $this->auth_key,
        ]);
    }
}
