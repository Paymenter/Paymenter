<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase;

use App\Livewire\Component;
use Livewire\Attributes\Url;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeArticle;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeCategory;

class Index extends Component
{
    public function mount()
    {
        if (!KnowledgeCategory::visible()->exists()) {
            abort(404);
        }
    }

    #[Url(except: '')]
    public string $search = '';

    public function updatedSearch()
    {
        $this->search = trim($this->search);
    }

    public function render()
    {
        $searchTerm = trim($this->search);

        $categories = KnowledgeCategory::visible()
            ->ordered()
            ->withCount(['publishedArticles as articles_count'])
            ->with(['publishedArticles' => fn($query) => $query->ordered()->limit(5)])
            ->get();

        return view('knowledgebase::index', [
            'categories' => $categories,
            'searchResults' => $searchTerm === ''
                ? collect()
                : KnowledgeArticle::published()
                ->ordered()
                ->search($searchTerm)
                ->with(['category:id,name,slug'])
                ->limit(10)
                ->get(),
            'searchTerm' => $searchTerm,
        ]);
    }
}
