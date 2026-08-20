<?php

namespace App\Models;

use App\Support\PushEndpoint;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Minishlink\WebPush\Subscription;

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
        if (!PushEndpoint::isAllowed($this->endpoint)) {
            throw new InvalidArgumentException('Push subscription endpoint is not allowed.');
        }

        return Subscription::create([
            'endpoint' => $this->endpoint,
            'publicKey' => $this->p256dh_key,
            'authToken' => $this->auth_key,
        ]);
    }
}
