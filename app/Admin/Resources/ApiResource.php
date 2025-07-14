<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ApiResource\Pages\ManageApis;
use App\Models\ApiKey;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

class ApiResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static string|\BackedEnum|null $navigationIcon = 'ri-key-2-line';

    protected static string|\UnitEnum|null $navigationGroup = 'Other';

    public static function form(Schema $schema): Schema
    {

        $extensionApiPermissions = once(fn () => Arr::dot(
            array_merge_recursive(...Event::dispatch('api.permissions', []))
        ));

        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('API Name'),
                TagsInput::make('ip_addresses')
                    ->label('Allowed IP Addresses')
                    ->placeholder('Enter IP addresses'),
                Toggle::make('enabled')
                    ->default(true)
                    ->visibleOn('edit')
                    ->label('Active Status'),

                CheckboxList::make('permissions')
                    ->options(array_merge(Arr::dot(config('permissions.api')), $extensionApiPermissions))
                    ->columns(4)
                    ->bulkToggleable()
                    ->searchable()
                    ->noSearchResultsMessage('Permission could not be found'),

            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('ip_addresses')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->label('Allowed IP Addresses'),
                IconColumn::make('enabled')
                    ->boolean(),
                TextColumn::make('last_used_at')
                    ->dateTime()
                    ->label('Last Used At')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => ManageApis::route('/'),
        ];
    }
}
