<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase;

use App\Livewire\Component;
use Illuminate\Support\Str;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeArticle;

class Show extends Component
{
    public KnowledgeArticle $article;
    public ?KnowledgeArticle $previousArticle = null;
    public ?KnowledgeArticle $nextArticle = null;

    public function mount(KnowledgeArticle $article): void
    {
        if (!$article->isPublished()) {
            abort(404);
        }

        $article->load('category');
        $article->recordView();

        $this->article = $article;

        [$this->previousArticle, $this->nextArticle] = $article->adjacentArticles();
    }

    public function render()
    {
        $article = $this->article;

        $description = $article->summary
            ?: Str::of(strip_tags($article->content))
                ->squish()
                ->limit(160)
                ->toString();

        $keywords = collect([
            $article->category?->name,
        ])->filter()->implode(', ');

        return view('knowledgebase::show', [
            'article' => $article,
            'previousArticle' => $this->previousArticle,
            'nextArticle' => $this->nextArticle,
        ])->layoutData([
            'title' => $article->title,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => route('knowledgebase.show', $article),
        ]);
    }
}
