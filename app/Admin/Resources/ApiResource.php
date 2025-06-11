<?php

namespace App\Admin\Resources;

use App\Admin\Resources\ApiResource\Pages;
use App\Models\ApiKey;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

class ApiResource extends Resource
{
    protected static ?string $model = ApiKey::class;

    protected static ?string $navigationIcon = 'ri-key-2-line';

    protected static ?string $navigationGroup = 'Other';

    public static function form(Form $form): Form
    {

        $extensionApiPermissions = once(fn () => Arr::dot(
            array_merge_recursive(...Event::dispatch('api.permissions', []))
        ));

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('API Name'),
                Forms\Components\TagsInput::make('ip_addresses')
                    ->label('Allowed IP Addresses')
                    ->placeholder('Enter IP addresses'),
                Forms\Components\Toggle::make('enabled')
                    ->default(true)
                    ->visibleOn('edit')
                    ->label('Active Status'),

                Forms\Components\CheckboxList::make('permissions')
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
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('ip_addresses')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->label('Allowed IP Addresses'),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('last_used_at')
                    ->dateTime()
                    ->label('Last Used At')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageApis::route('/'),
        ];
    }
}
