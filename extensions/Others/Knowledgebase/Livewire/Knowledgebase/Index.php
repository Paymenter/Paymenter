<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase;

use App\Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeArticle;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeCategory;

class Index extends Component
{
    use WithPagination;

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
        $this->resetPage('searchPage');
    }

    public function render()
    {
        $searchTerm = trim($this->search);

        $categories = KnowledgeCategory::visible()
            ->whereNull('parent_id')
            ->ordered()
            ->withCount(['publishedArticles as articles_count'])
            ->with([
                'children' => fn ($query) => $query->visible()
                    ->ordered()
                    ->withCount(['publishedArticles as articles_count']),
                'publishedArticles' => fn ($query) => $query->ordered()->limit(5),
            ])
            ->get()
            ->each(function (KnowledgeCategory $category) {
                $category->total_articles_count = $category->articles_count + $category->children->sum('articles_count');
            });

        $searchResults = null;

        if ($searchTerm !== '') {
            $lowerTerm = Str::lower($searchTerm);

            $perPage = (int) config('settings.pagination', 10);
            if ($perPage <= 0) {
                $perPage = 10;
            }

            $searchResults = KnowledgeArticle::published()
                ->ordered()
                ->search($searchTerm)
                ->with(['category:id,name,slug,parent_id'])
                ->paginate($perPage, ['*'], 'searchPage');

            $matchingCategoryIds = collect($searchResults->items())
                ->pluck('category_id')
                ->filter()
                ->unique();
            $matchingCategoryIds = $matchingCategoryIds->merge(
                collect($searchResults->items())->pluck('category.parent_id')->filter()
            )->unique();

            $categories = $categories->filter(function (KnowledgeCategory $category) use ($lowerTerm, $matchingCategoryIds) {
                $description = Str::lower(strip_tags($category->description ?? ''));

                $matchesCategoryText = Str::contains(Str::lower($category->name), $lowerTerm)
                    || ($description !== '' && Str::contains($description, $lowerTerm));

                $matchesArticles = $matchingCategoryIds->contains($category->id);
                $matchesChild = $category->children->pluck('id')->intersect($matchingCategoryIds)->isNotEmpty();

                return $matchesCategoryText || $matchesArticles || $matchesChild;
            })->values();
        }

        return view('knowledgebase::index', [
            'categories' => $categories,
            'searchResults' => $searchResults,
            'searchTerm' => $searchTerm,
        ]);
    }
}
