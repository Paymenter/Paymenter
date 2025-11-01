<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase;

use App\Livewire\Component;
use Illuminate\Support\Str;
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
        $category = $this->category;

        $description = Str::of($category->description ?? '')
            ->stripTags()
            ->squish()
            ->limit(160)
            ->toString();

        $articles = $category->publishedArticles;

        $keywords = collect([$category->name])
            ->merge($articles->pluck('title')->take(5))
            ->filter()
            ->implode(', ');

        return view('knowledgebase::category', [
            'category' => $category,
            'articles' => $articles,
        ])->layoutData([
            'title' => $category->name,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => route('knowledgebase.category', $category),
        ]);
    }
}
