<?php

namespace App\Admin\Resources\CategoryResource\RelationManagers;

use App\Admin\Resources\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    protected static ?string $relatedResource = ProductResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('sort')
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
