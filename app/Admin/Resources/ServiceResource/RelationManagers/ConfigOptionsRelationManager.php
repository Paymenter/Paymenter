<?php

namespace App\Admin\Resources\ServiceResource\RelationManagers;

use App\Models\ServiceConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConfigOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'configs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('configValue.id')
                    ->label('Config Value')
                    ->required()
                    ->relationship('configValue', 'id', fn (Builder $query, ServiceConfig $record) => $query->where('parent_id', $record->config_option_id))
                    ->searchable()
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->getSearchResultsUsing(fn (string $search, ServiceConfig $record): array => $record->configOption->children()->where('name', 'like', "%$search%")->limit(50)->pluck('name', 'id')->toArray())
                    ->live()
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('configOption.name')
            ->columns([
                Tables\Columns\TextColumn::make('configOption.name'),
                Tables\Columns\TextColumn::make('configValue.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
