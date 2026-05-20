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

    public static function getNavigationLabel(): string
    {
        return __('apis.api_keys');
    }

    public static function getModelLabel(): string
    {
        return __('apis.api_key');
    }

    public static function getPluralModelLabel(): string
    {
        return __('apis.api_keys');
    }

    public static function form(Schema $schema): Schema
    {

        $extensionApiPermissions = once(fn () => Arr::dot(
            array_merge_recursive(...Event::dispatch('api.permissions', []))
        ));

        $permissions = array_merge(Arr::dot(config('permissions.api')), $extensionApiPermissions);
        $translatedPermissions = [];
        foreach ($permissions as $key => $defaultLabel) {
            $translationKey = 'permissions.' . $key;
            $translatedLabel = __($translationKey);
            $translatedPermissions[$key] = ($translatedLabel !== $translationKey) ? $translatedLabel : $defaultLabel;
        }

        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label(__('apis.api_name')),
                TagsInput::make('ip_addresses')
                    ->label(__('apis.allowed_ips'))
                    ->placeholder(__('apis.enter_ips')),
                Toggle::make('enabled')
                    ->default(true)
                    ->visibleOn('edit')
                    ->label(__('apis.active_status')),

                CheckboxList::make('permissions')
                    ->options($translatedPermissions)
                    ->columns(4)
                    ->bulkToggleable()
                    ->searchable()
                    ->noSearchResultsMessage(__('apis.no_permission_found')),

            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('apis.name')),
                TextColumn::make('ip_addresses')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : $state)
                    ->label(__('apis.allowed_ips')),
                IconColumn::make('enabled')
                    ->label(__('apis.active_status'))
                    ->boolean(),
                TextColumn::make('last_used_at')
                    ->dateTime()
                    ->label(__('apis.last_used_at'))
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
