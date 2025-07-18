<?php

namespace App\Admin\Resources;

use App\Models\Role;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Event;
use Filament\Forms\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use App\Admin\Resources\RoleResource\Pages;
use Filament\Forms\Components\CheckboxList;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $navigationIcon = 'ri-shield-user-line';

    protected static ?string $activeNavigationIcon = 'ri-shield-user-fill';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                // Split permission UI into core and extension
                Fieldset::make('Core Permissions')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label(false)
                            ->options(static::getCorePermissions())
                            ->columns(4)
                            ->bulkToggleable()
                            ->searchable()
                            ->noSearchResultsMessage('Permission could not be found')->columnSpanFull(),
                    ]),

                Fieldset::make('Extension Permissions')
                    ->schema([
                        CheckboxList::make('permissions')
                            ->label(false)
                            ->options(static::getExtensionPermissions())
                            ->columns(4)
                            ->bulkToggleable()
                            ->searchable()
                            ->noSearchResultsMessage('Permission could not be found')->columnSpanFull(),
                    ]),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable(),
                TextColumn::make('permissions')->formatStateUsing(fn (Role $record): string => in_array('*', $record->permissions) ? 'All' : count($record->permissions))->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return $record->id !== 1;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    protected static function getCorePermissions(): array
    {
        return Arr::dot(config('permissions.role'));
    }

    protected static function getExtensionPermissions(): array
    {
        return collect(Event::dispatch('permissions'))
            ->filter(fn($response) => is_array($response))
            ->flatMap(fn($permissions) => $permissions)
            ->pipe(fn($merged) => Arr::dot($merged));
    }
}
