<?php

namespace App\Admin\Resources\ProductResource\Pages;

use App\Admin\Resources\ProductResource;
use App\Models\Category;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->reorderAction(),
            CreateAction::make(),
        ];
    }

    /**
     * Products are sorted within their category, so reordering starts by picking the category to
     * reorder. Narrowing the list down to it is what makes the table reorderable, after which the
     * table shows its own action to leave reordering again.
     */
    protected function reorderAction(): Action
    {
        return Action::make('reorder')
            ->label('Reorder')
            ->icon('ri-arrow-up-down-line')
            ->color('gray')
            ->visible(fn (): bool => !$this->isTableReordering() && Auth::user()->hasPermission('admin.products.update'))
            ->modalHeading('Reorder products')
            ->modalDescription('Products are ordered within their own category.')
            ->modalSubmitActionLabel('Reorder')
            ->schema([
                Select::make('category')
                    ->label('Category')
                    ->options(fn (): array => Category::orderBy('name')->pluck('name', 'id')->all())
                    ->default(fn () => $this->tableFilters['category']['value'] ?? null)
                    ->searchable()
                    ->required(),
            ])
            ->action(function (array $data): void {
                $this->tableFilters['category']['value'] = $data['category'];
                $this->resetPage();
                $this->toggleTableReordering();
            });
    }
}
