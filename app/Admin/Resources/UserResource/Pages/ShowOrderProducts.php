<?php

namespace App\Admin\Resources\UserResource\Pages;

use App\Admin\Resources\OrderProductResource;
use App\Admin\Resources\UserResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;

class ShowOrderProducts extends ManageRelatedRecords
{
    protected static string $resource = UserResource::class;

    protected static string $relationship = 'orderProducts';

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function getNavigationLabel(): string
    {
        return 'Products/Services';
    }

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
                Tables\Actions\ViewAction::make()->url(fn ($record) => OrderProductResource::getUrl('edit', ['record' => $record])),
            ]);
    }
}
