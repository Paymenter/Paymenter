<?php

namespace Paymenter\Extensions\Others\CyberpunkTheme\Models;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Paymenter\Extensions\Others\CyberpunkTheme\Support\Config;

class Comment extends Model
{
    /**
     * Destino de las reseñas sobre el servicio en general (no sobre un plan
     * concreto). No es una clase de verdad: sólo una etiqueta para poder
     * guardarlas en la misma tabla polimórfica.
     */
    public const GENERAL = 'cyberpunk:general';

    protected $table = 'ext_cyberpunk_comments';

    protected $guarded = [];

    protected $casts = [
        'approved' => 'boolean',
        'featured' => 'boolean',
        'rating' => 'integer',
    ];

    /**
     * ¿Es una reseña (lleva estrellas) y no un comentario suelto?
     */
    public function isReview(): bool
    {
        return $this->parent_id === null && $this->rating !== null && $this->rating > 0;
    }

    /**
     * Reseñas: sólo las de primer nivel, aprobadas y con estrellas.
     */
    public function scopeReviews($query)
    {
        return $query->whereNull('parent_id')
            ->where('approved', true)
            ->whereNotNull('rating')
            ->where('rating', '>', 0);
    }

    /**
     * Reseñas del servicio en general.
     */
    public function scopeGeneral($query)
    {
        return $query->where('commentable_type', self::GENERAL);
    }

    /**
     * Nombre legible del destino de la reseña.
     */
    public function targetLabel(): string
    {
        if ($this->commentable_type === self::GENERAL) {
            return (string) Config::theme('general_reviews_name', 'El servicio en general');
        }

        try {
            if ($this->commentable_type === Product::class) {
                return Product::find($this->commentable_id)?->name ?? 'Plan';
            }
        } catch (\Throwable $e) {
            // Da igual: usamos la etiqueta genérica.
        }

        return 'Publicación';
    }

    /**
     * Al borrar un comentario se borran también sus respuestas y sus likes.
     */
    protected static function booted(): void
    {
        static::deleting(function (self $comment) {
            $comment->likes()->delete();

            foreach ($comment->replies as $reply) {
                $reply->delete();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
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
