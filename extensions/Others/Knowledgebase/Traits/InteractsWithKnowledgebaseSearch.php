<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Traits;

use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeArticle;

trait InteractsWithKnowledgebaseSearch
{
    public function updatedSearch(): void
    {
        $this->search = trim($this->search ?? '');
        if (method_exists($this, 'resetPage')) {
            $this->resetPage('searchPage');
        }
    }

    protected function resolveSearchTerm(?string $term = null): string
    {
        return trim($term ?? $this->search ?? '');
    }

    protected function resolvePerPage(): int
    {
        $perPage = (int) config('settings.pagination', 10);

        return $perPage > 0 ? $perPage : 10;
    }

    protected function fetchSearchResults(?string $term = null)
    {
        $term = $this->resolveSearchTerm($term);

        if ($term === '') {
            return null;
        }

        $perPage = $this->resolvePerPage();

        return KnowledgeArticle::published()
            ->ordered()
            ->search($term)
            ->with(['category:id,name,slug,parent_id'])
            ->paginate($perPage, ['*'], 'searchPage');
    }
}
