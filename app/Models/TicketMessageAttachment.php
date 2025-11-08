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
        // Security: Validate path to prevent directory traversal
        $safePath = str_replace(['..', "\0"], '', $this->path);
        $fullPath = storage_path("app/{$safePath}");

        // Ensure the resolved path is within the storage directory
        $storagePath = storage_path('app/');
        if (strpos(realpath(dirname($fullPath)), realpath($storagePath)) !== 0) {
            throw new \RuntimeException('Invalid file path');
        }

        return $fullPath;
    }

    // Function to check if attachment can be previewed
    public function canPreview(): bool
    {
        return Str::startsWith($this->mime_type, ['image/']);
    }
}
