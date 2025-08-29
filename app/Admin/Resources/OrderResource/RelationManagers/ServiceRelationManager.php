<?php

namespace App\Admin\Resources\OrderResource\RelationManagers;

use App\Admin\Resources\ServiceResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ServiceRelationManager extends RelationManager
{
    protected static string $relationship = 'services';

    // Renaem to Order Products
    public static string $name = 'Products/Services';

    public static ?string $label = 'Products/Services';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('product.name'),
                TextColumn::make('quantity'),
                TextColumn::make('formattedPrice')->label('Price'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()->url(fn ($record) => ServiceResource::getUrl('edit', ['record' => $record])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
