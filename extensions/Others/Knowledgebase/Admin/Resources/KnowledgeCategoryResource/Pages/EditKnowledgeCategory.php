<?php

namespace Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Paymenter\Extensions\Others\Knowledgebase\Admin\Resources\KnowledgeCategoryResource;

class EditKnowledgeCategory extends EditRecord
{
    protected static string $resource = KnowledgeCategoryResource::class;

    protected static ?string $title = 'Knowledgebase';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->requiresConfirmation()
                ->disabled(fn ($record) => $record->articles()->exists())
                ->tooltip('Remove all articles from the category before deleting it.')
                ->before(function (DeleteAction $action, $record) {
                    if ($record->articles()->exists()) {
                        Notification::make()
                            ->title('Cannot delete category')
                            ->body('Remove all articles from the category before deleting it.')
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
        ];
    }

    public function getHeading(): string
    {
        return $this->getRecord()->name ?? 'Category';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return null;
    }

    public function getBreadcrumbs(): array
    {
        $categoryName = $this->getRecord()->name ?? 'Category';

        return [
            KnowledgeCategoryResource::getUrl('index') => 'Knowledgebase',
            'Categories',
            $categoryName,
        ];
    }
}
