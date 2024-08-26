<?php

namespace App\Admin\Resources\Common\RelationManagers;

use App\Models\CustomProperty;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PropertiesRelationManager extends RelationManager
{
    protected static string $relationship = 'properties';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('custom_property_id')->label('Custom Property')
                    ->required()
                    ->options(function ($livewire): array {
                        return CustomProperty::where('model', get_class($livewire->ownerRecord))->pluck('name', 'id')->toArray();
                    })->nullable(),
                Forms\Components\TextInput::make('name')->translateLabel()->nullable(),
                Forms\Components\TextInput::make('key')->translateLabel()->required(),
                Forms\Components\TextInput::make('value')->translateLabel()->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('parent_property.name'),
                Tables\Columns\TextColumn::make('key'),
                Tables\Columns\TextInputColumn::make('value'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
