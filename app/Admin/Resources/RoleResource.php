<?php

namespace App\Admin\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use App\Admin\Resources\RoleResource\Pages\ListRoles;
use App\Admin\Resources\RoleResource\Pages\CreateRole;
use App\Admin\Resources\RoleResource\Pages\EditRole;
use App\Admin\Resources\RoleResource\Pages;
use App\Models\Role;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Configuration';

    protected static string | \BackedEnum | null $navigationIcon = 'ri-shield-user-line';

    protected static string | \BackedEnum | null $activeNavigationIcon = 'ri-shield-user-fill';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                CheckboxList::make('permissions')->options(Arr::dot(config('permissions.role')))->columns(4)->bulkToggleable()->searchable()->noSearchResultsMessage('Permission could not be found'),
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
            ->recordActions([
                EditAction::make(),
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
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }
}
