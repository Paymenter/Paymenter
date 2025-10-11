<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'mail_enabled',
        'in_app_enabled',
        'notification_template_id',
    ];

    protected $casts = [
        'mail_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
    ];

    public function notificationTemplate()
    {
        return $this->belongsTo(NotificationTemplate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
