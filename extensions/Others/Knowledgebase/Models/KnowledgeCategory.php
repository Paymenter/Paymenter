<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KnowledgeCategory extends Model
{
    protected $table = 'ext_kb_categories';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
        'parent_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(KnowledgeArticle::class, 'category_id')
            ->orderBy('sort_order')
            ->orderBy('title');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function publishedArticles(): HasMany
    {
        return $this->articles()->published();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeWithPublishedArticles(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereHas('publishedArticles')
                ->orWhereHas('children', fn (Builder $childQuery) => $childQuery
                    ->active()
                    ->whereHas('publishedArticles'));
        });
    }

    public function scopeMatchesSearchTerm(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        $like = '%' . $term . '%';

        return $query->where(function (Builder $query) use ($term, $like) {
            $query->where('name', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhereHas('publishedArticles', fn (Builder $articleQuery) => $articleQuery->search($term))
                ->orWhereHas('children', fn (Builder $childQuery) => $childQuery->matchesSearchTerm($term));
        });
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->active()->withPublishedArticles();
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
