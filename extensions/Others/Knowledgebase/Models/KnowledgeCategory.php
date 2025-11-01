<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
        return $query->whereHas('publishedArticles');
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
