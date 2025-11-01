<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource;

class EditKnowledgeArticle extends EditRecord
{
    protected static string $resource = KnowledgeArticleResource::class;

    protected static ?string $title = 'Knowledgebase';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getHeading(): string
    {
        return $this->getRecord()->title ?? 'Article';
    }

    public function getSubheading(): string | Htmlable | null
    {
        return null;
    }

    public function getBreadcrumbs(): array
    {
        $articleTitle = $this->getRecord()->title ?? 'Article';

        return [
            KnowledgeArticleResource::getUrl('index') => 'Knowledgebase',
            'Articles',
            $articleTitle,
        ];
    }
}
