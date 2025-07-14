<?php

namespace App\Admin\Resources\Common\RelationManagers;

use App\Models\CustomProperty;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class PropertiesRelationManager extends RelationManager
{
    protected static string $relationship = 'properties';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('custom_property_id')->label('Custom Property')
                    ->required()
                    ->options(function ($livewire): array {
                        return CustomProperty::where('model', get_class($livewire->ownerRecord))->pluck('name', 'id')->toArray();
                    })->nullable(),
                TextInput::make('name')->translateLabel()->nullable(),
                TextInput::make('key')->translateLabel()->required(),
                TextInput::make('value')->translateLabel()->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('parent_property.name'),
                TextColumn::make('key'),
                TextInputColumn::make('value'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
