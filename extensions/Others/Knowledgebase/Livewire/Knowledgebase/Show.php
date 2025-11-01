<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase;

use App\Livewire\Component;
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
        return view('knowledgebase::show', [
            'article' => $this->article,
            'previousArticle' => $this->previousArticle,
            'nextArticle' => $this->nextArticle,
        ]);
    }
}
