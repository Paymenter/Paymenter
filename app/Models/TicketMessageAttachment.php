<?php

namespace App\Models;

use Illuminate\Support\Str;

class TicketMessageAttachment extends Model
{
    protected $fillable = [
        'path',
        'filename',
        'filesize',
        'mime_type',
        'ticket_message_id',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function ticketMessage()
    {
        return $this->belongsTo(TicketMessage::class);
    }

    public function getLocalPathAttribute(): string
    {
        return storage_path("app/{$this->path}");
    }

    // Function to check if attachment can be previewed
    public function canPreview(): bool
    {
        return Str::startsWith($this->mime_type, ['image/']);
    }
}
