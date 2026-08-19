<?php

namespace App\Admin\Resources\Common\RelationManagers;

use App\Models\CustomProperty;
use App\Models\Service;
use App\Support\ServiceAdminAuthorization;
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
use Illuminate\Database\Eloquent\Model;

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

    public function isReadOnly(): bool
    {
        return !$this->canWriteOwner();
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('parent_property.name'),
                TextColumn::make('key'),
                $this->isReadOnly()
                    ? TextColumn::make('value')
                    : TextInputColumn::make('value')
                        ->updateStateUsing(function (Model $record, mixed $state): mixed {
                            abort_unless($this->canWriteOwner(), 403);
                            $record->update(['value' => $state]);

                            return $state;
                        }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->authorize(fn (): bool => $this->canWriteOwner()),
            ])
            ->recordActions([
                EditAction::make()
                    ->authorize(fn (): bool => $this->canWriteOwner()),
                DeleteAction::make()
                    ->authorize(fn (): bool => $this->canWriteOwner()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->authorize(fn (): bool => $this->canWriteOwner()),
                ]),
            ]);
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
