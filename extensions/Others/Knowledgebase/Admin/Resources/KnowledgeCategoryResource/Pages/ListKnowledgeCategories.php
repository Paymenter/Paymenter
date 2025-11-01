<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource;

class ListKnowledgeCategories extends ListRecords
{
    protected static string $resource = KnowledgeCategoryResource::class;

    protected ?string $heading = 'Categories';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create category'),
        ];
    }

    public function getTitle(): string
    {
        return 'Knowledgebase';
    }

    public function getHeading(): string
    {
        return $this->heading ?? parent::getHeading();
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Knowledgebase',
            'Categories',
        ];
    }
}
