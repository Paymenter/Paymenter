<?php

namespace App\Admin\Resources\ServiceResource\RelationManagers;

use App\Models\Service;
use App\Models\ServiceConfig;
use App\Support\ServiceAdminAuthorization;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ConfigOptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'configs';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('configValue.id')
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
                TextColumn::make('configOption.name'),
                TextColumn::make('configValue.name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([])
            ->recordActions([
                EditAction::make()
                    ->authorize(fn (): bool => $this->canWriteOwner()),
                DeleteAction::make()
                    ->authorize(fn (): bool => $this->canWriteOwner()),
            ]);
    }

    public function isReadOnly(): bool
    {
        return !$this->canWriteOwner();
    }

    protected function canWriteOwner(): bool
    {
        $owner = $this->getOwnerRecord();
        $user = auth()->user();

        if (!$owner instanceof Model || !$user) {
            return false;
        }

        if ($owner instanceof Service) {
            return ServiceAdminAuthorization::canUpdate($user, $owner);
        }

        return $user->can('update', $owner);
    }
}
