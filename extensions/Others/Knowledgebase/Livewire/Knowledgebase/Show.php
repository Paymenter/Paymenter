<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase;

use App\Helpers\ExtensionHelper;
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

        $allowDownloads = $this->resolveDownloadsEnabled();

        return view('knowledgebase::show', [
            'article' => $article,
            'previousArticle' => $this->previousArticle,
            'nextArticle' => $this->nextArticle,
            'allowDownloads' => $allowDownloads,
        ])->layoutData([
            'title' => $article->title,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => route('knowledgebase.show', $article),
        ]);
    }

    protected function resolveDownloadsEnabled(): bool
    {
        $extension = ExtensionHelper::getExtension('other', 'Knowledgebase');

        if (!$extension) {
            return true;
        }

        $raw = $extension->config('allow_downloads');

        if ($raw === null) {
            return true;
        }

        $parsed = filter_var($raw, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        return $parsed ?? (bool) $raw;
    }
}
