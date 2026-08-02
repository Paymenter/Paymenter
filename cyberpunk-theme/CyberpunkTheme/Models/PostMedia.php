<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class PostMedia extends Model
{
    protected $table = 'ext_cyberpunk_post_media';

    protected $guarded = [];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
