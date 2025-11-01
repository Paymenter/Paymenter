<?php

namespace Paymenter\Extensions\Others\Knowledgebase;

use App\Classes\Extension\Extension;
use App\Helpers\ExtensionHelper;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\HtmlString;
use Livewire\Livewire;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource;
use Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase\Category as CategoryComponent;
use Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase\Index;
use Paymenter\Extensions\Others\Knowledgebase\Livewire\Knowledgebase\Show;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeArticle;
use Paymenter\Extensions\Others\Knowledgebase\Models\KnowledgeCategory;
use Paymenter\Extensions\Others\Knowledgebase\Policies\KnowledgeArticlePolicy;
use Paymenter\Extensions\Others\Knowledgebase\Policies\KnowledgeCategoryPolicy;

class Knowledgebase extends Extension
{
    public function getConfig($values = [])
    {
        try {
            return [
                [
                    'name' => 'notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('Use this extension to publish knowledge base articles. Manage them from <a class="text-primary-600" href="' . KnowledgeArticleResource::getUrl() . '">Knowledge Base</a>.'),
                ],
            ];
        } catch (\Throwable $e) {
            return [
                [
                    'name' => 'notice',
                    'type' => 'placeholder',
                    'label' => new HtmlString('Enable the extension to manage knowledge base articles from the admin area.'),
                ],
            ];
        }
    }

    public function installed()
    {
        ExtensionHelper::runMigrations('extensions/Others/Knowledgebase/database/migrations');
    }

    public function uninstalled()
    {
        ExtensionHelper::rollbackMigrations('extensions/Others/Knowledgebase/database/migrations');
    }

    public function boot()
    {
        require __DIR__ . '/routes/web.php';

        View::addNamespace('knowledgebase', __DIR__ . '/resources/views');
        Lang::addNamespace('knowledgebase', __DIR__ . '/resources/lang');

        Livewire::component('knowledgebase.index', Index::class);
        Livewire::component('knowledgebase.category', CategoryComponent::class);
        Livewire::component('knowledgebase.show', Show::class);

        Gate::policy(KnowledgeCategory::class, KnowledgeCategoryPolicy::class);
        Gate::policy(KnowledgeArticle::class, KnowledgeArticlePolicy::class);

        Event::listen('navigation', function () {
            if (!Schema::hasTable('ext_kb_articles')) {
                return;
            }

            if (!KnowledgeArticle::published()->exists()) {
                return;
            }

            return [
                'name' => 'Knowledgebase',
                'route' => 'knowledgebase.index',
                'icon' => 'ri-article',
                'priority' => 15,
            ];
        });

        Event::listen('permissions', function () {
            return [
                'admin.knowledgebase.categories.view' => 'View Knowledgebase Categories',
                'admin.knowledgebase.categories.create' => 'Create Knowledgebase Categories',
                'admin.knowledgebase.categories.update' => 'Update Knowledgebase Categories',
                'admin.knowledgebase.categories.delete' => 'Delete Knowledgebase Categories',
                'admin.knowledgebase.articles.view' => 'View Knowledgebase Articles',
                'admin.knowledgebase.articles.create' => 'Create Knowledgebase Articles',
                'admin.knowledgebase.articles.update' => 'Update Knowledgebase Articles',
                'admin.knowledgebase.articles.delete' => 'Delete Knowledgebase Articles',
            ];
        });
    }
}
