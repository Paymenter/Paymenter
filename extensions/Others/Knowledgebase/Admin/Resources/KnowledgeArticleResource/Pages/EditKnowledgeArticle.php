<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeArticleResource;

class EditKnowledgeArticle extends EditRecord
{
    protected static string $resource = KnowledgeArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
