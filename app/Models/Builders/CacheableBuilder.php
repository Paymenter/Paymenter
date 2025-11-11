<?php

namespace App\Models\Builders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class CacheableBuilder extends Builder
{
    public function get($columns = ['*'])
    {
        $cacheKey = $this->generateCacheKey();

        return Cache::remember($cacheKey, 3600, function () use ($columns) {
            return parent::get($columns);
        });
    }

    public function first($columns = ['*'])
    {
        $cacheKey = $this->generateCacheKey() . '_first';

        return Cache::remember($cacheKey, 3600, function () use ($columns) {
            return parent::first($columns);
        });
    }

    protected function generateCacheKey()
    {
        // Unique key based on SQL + bindings
        return 'query_' . md5($this->toSql() . implode(',', $this->getBindings()));
    }

    public function clearCache()
    {
        $cacheKey = $this->generateCacheKey();
        Cache::forget($cacheKey);
    }
}
