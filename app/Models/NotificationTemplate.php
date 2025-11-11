<?php

namespace App\Models;

use App\Enums\NotificationEnabledStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class NotificationTemplate extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    protected $fillable = [
        'key',
        'name',
        'subject',
        'enabled',
        'body',
        'cc',
        'bcc',
        'mail_enabled',
        'in_app_enabled',
        'in_app_title',
        'in_app_body',
        'edit_preference_message',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'cc' => 'array',
        'bcc' => 'array',
        'mail_enabled' => NotificationEnabledStatus::class,
        'in_app_enabled' => NotificationEnabledStatus::class,
    ];

    public function preferences()
    {
        return $this->hasMany(NotificationPreference::class);
    }

    public function isEmailUserControllable()
    {
        return in_array($this->mail_enabled, [NotificationEnabledStatus::ChoiceOn, NotificationEnabledStatus::ChoiceOff]);
    }

    public function isInAppUserControllable()
    {
        return in_array($this->in_app_enabled, [NotificationEnabledStatus::ChoiceOn, NotificationEnabledStatus::ChoiceOff]);
    }

    // Check if user has enabled this notification for email
    public function isEnabledForPreference(?NotificationPreference $preference = null, $type = 'mail')
    {
        $type = $type === 'app' ? 'in_app_enabled' : 'mail_enabled';
        if ($this->{$type} === NotificationEnabledStatus::Force) {
            return true;
        }
        if ($this->{$type} === NotificationEnabledStatus::Never) {
            return false;
        }

        if ($preference) {
            return $preference->{$type};
        }

        // Return true if choice_on, false if choice_off
        return $this->{$type} === NotificationEnabledStatus::ChoiceOn;
    }
}
