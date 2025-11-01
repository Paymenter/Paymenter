<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource;

class ListKnowledgeArticles extends ListRecords
{
    protected static string $resource = KnowledgeArticleResource::class;

    protected static ?string $title = 'Articles';

    protected ?string $heading = 'Articles';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            KnowledgeArticleResource::getUrl('index') => 'Knowledgebase',
            'Articles',
        ];
    }
}
