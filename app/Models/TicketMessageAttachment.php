<?php

namespace App\Models;

use Illuminate\Support\Str;

class TicketMessageAttachment extends Model
{
    /** Maximum size of an attachment in kilobytes */
    public const MAX_SIZE = 10240;

    /** Allowed file types until the "ticket_attachment_extensions" setting is saved */
    public const DEFAULT_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'txt', 'log', 'zip'];

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
            $model->uuid = (string) Str::uuid();
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

    /**
     * File extensions clients may attach to tickets (empty = any file type)
     */
    public static function allowedExtensions(): array
    {
        return collect((array) config('settings.ticket_attachment_extensions', self::DEFAULT_EXTENSIONS))
            ->map(fn ($extension) => strtolower(ltrim(trim((string) $extension), '.')))
            ->filter(fn ($extension) => preg_match('/^[a-z0-9]+$/', $extension))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Validation rules for an uploaded attachment
     */
    public static function validationRules(): array
    {
        $rules = ['file', 'max:' . self::MAX_SIZE];

        if ($extensions = self::allowedExtensions()) {
            // Both the extension of the name and the type of the content must be allowed
            $rules[] = 'extensions:' . implode(',', $extensions);
            $rules[] = 'mimes:' . implode(',', $extensions);
        }

        return $rules;
    }

    /**
     * Whether an attachment received by email may be saved
     */
    public static function isAllowed(?string $filename, int $size): bool
    {
        if ($size > self::MAX_SIZE * 1024) {
            return false;
        }

        $extensions = self::allowedExtensions();

        return !$extensions || in_array(strtolower(pathinfo((string) $filename, PATHINFO_EXTENSION)), $extensions, true);
    }
}
