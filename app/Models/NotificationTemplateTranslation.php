<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationTemplateTranslation extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_template_id',
        'locale',
        'subject',
        'body',
        'in_app_title',
        'in_app_body',
        'in_app_url',
        'edit_preference_message',
    ];

    /**
     * @return BelongsTo<NotificationTemplate, $this>
     */
    public function notificationTemplate(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class);
    }
}
