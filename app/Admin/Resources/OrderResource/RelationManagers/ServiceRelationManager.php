<?php

namespace App\Admin\Resources\OrderResource\RelationManagers;

use App\Admin\Resources\ServiceResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
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
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\TextColumn::make('quantity'),
                Tables\Columns\TextColumn::make('formattedPrice')->label('Price'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->url(fn ($record) => ServiceResource::getUrl('edit', ['record' => $record])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
