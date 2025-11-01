<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase;

use App\Livewire\Component;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeCategory;

class Category extends Component
{
    public KnowledgeCategory $category;

    public function mount(KnowledgeCategory $category)
    {
        $category->load(['publishedArticles' => fn($query) => $query->ordered()]);

        abort_if(!$category->is_active || $category->publishedArticles->isEmpty(), 404);

        $this->category = $category;
    }

    public function render()
    {
        return view('knowledgebase::category', [
            'category' => $this->category,
            'articles' => $this->category->publishedArticles,
        ]);
    }
}
