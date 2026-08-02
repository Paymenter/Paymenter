<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $table = 'ext_cyberpunk_posts';

    protected $guarded = [];

    protected $casts = [
        'approved' => 'boolean',
        'pinned' => 'boolean',
    ];

    /**
     * Al borrar una publicación limpiamos sus archivos, comentarios y likes
     * (también cuando se borra desde el panel de administración).
     */
    protected static function booted(): void
    {
        static::deleting(function (self $post) {
            foreach ($post->media as $item) {
                try {
                    Storage::disk('public')->delete($item->path);
                } catch (\Throwable $e) {
                    // Ignoramos errores al borrar ficheros.
                }
            }

            $post->comments()->delete();
            $post->likes()->delete();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(PostMedia::class, 'post_id')->orderBy('sort');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->likes()->where('user_id', $user->id)->exists();
    }
}
