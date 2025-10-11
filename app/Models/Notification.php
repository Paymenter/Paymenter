<?php

namespace App\Models;

use App\Observers\NotificationObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([NotificationObserver::class])]
class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->read_at = now();
        $this->save();
    }
}
