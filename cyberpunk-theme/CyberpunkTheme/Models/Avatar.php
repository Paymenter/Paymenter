<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Avatar extends Model
{
    protected $table = 'ext_cyberpunk_avatars';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
