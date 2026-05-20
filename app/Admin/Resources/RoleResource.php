<?php

namespace App\Admin\Resources;

use App\Admin\Resources\RoleResource\Pages\CreateRole;
use App\Admin\Resources\RoleResource\Pages\EditRole;
use App\Admin\Resources\RoleResource\Pages\ListRoles;
use App\Models\Role;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    public static function getNavigationLabel(): string
    {
        return __('roles.roles');
    }

    public static function getModelLabel(): string
    {
        return __('roles.role_label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('roles.roles_plural_label');
    }

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-shield-user-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-shield-user-fill';

    public static function form(Schema $schema): Schema
    {
        $extensionPermissions = once(fn () => Arr::dot(
            array_merge_recursive(...Event::dispatch('permissions', []))
        ));

        $permissions = array_merge(Arr::dot(config('permissions.role')), $extensionPermissions);
        $translatedPermissions = [];
        foreach ($permissions as $key => $defaultLabel) {
            $translationKey = 'permissions.' . $key;
            $translatedLabel = __($translationKey);
            $translatedPermissions[$key] = ($translatedLabel !== $translationKey) ? $translatedLabel : $defaultLabel;
        }

        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('roles.name'))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                // Split permission UI into core and extension
                CheckboxList::make('permissions')
                    ->label(false)
                    ->options($translatedPermissions)
                    ->columns(4)
                    ->bulkToggleable()
                    ->searchable()
                    ->noSearchResultsMessage(__('roles.permission_not_found'))->columnSpanFull(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('roles.name'))
                    ->sortable(),
                TextColumn::make('permissions_count')
                    ->label(__('roles.permissions'))
                    ->getStateUsing(fn (Role $record): string => in_array('*', $record->permissions) ? __('roles.all') : (string) count($record->permissions))
                    ->sortable(),
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
