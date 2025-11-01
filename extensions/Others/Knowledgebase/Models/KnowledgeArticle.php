<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeArticle extends Model
{
    protected $table = 'ext_kb_articles';

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'summary',
        'content',
        'is_active',
        'sort_order',
        'published_at',
        'view_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_active', true)
            ->where(function (Builder $publishedQuery) {
                $publishedQuery
                    ->whereNull('published_at')
                    ->orWhere('published_at', '<=', Carbon::now());
            });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        $like = '%' . $term . '%';

        return $query->where(function (Builder $searchQuery) use ($like) {
            $searchQuery
                ->where('title', 'like', $like)
                ->orWhere('summary', 'like', $like)
                ->orWhere('content', 'like', $like);
        });
    }

    public function scopeForCategory(Builder $query, KnowledgeCategory $category): Builder
    {
        return $query->whereBelongsTo($category, 'category');
    }

    public function isPublished(): bool
    {
        return $this->is_active && (!$this->published_at || !$this->published_at->isFuture());
    }

    public function recordView(): void
    {
        $this->increment('view_count');
    }

    public function adjacentArticles(): array
    {
        $siblings = self::published()
            ->forCategory($this->category ?? $this->loadMissing('category')->category)
            ->select(['id', 'title', 'slug', 'category_id'])
            ->ordered()
            ->get();

        $index = $siblings->search(fn(KnowledgeArticle $item) => $item->id === $this->id);

        if ($index === false) {
            return [null, null];
        }

        return [
            $siblings->get($index - 1),
            $siblings->get($index + 1),
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
