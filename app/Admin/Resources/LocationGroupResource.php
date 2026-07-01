<?php

namespace App\Admin\Resources;

use App\Admin\Resources\LocationGroupResource\Pages\CreateLocationGroup;
use App\Admin\Resources\LocationGroupResource\Pages\EditLocationGroup;
use App\Admin\Resources\LocationGroupResource\Pages\ListLocationGroups;
use App\Models\LocationGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LocationGroupResource extends Resource
{
    protected static ?string $model = LocationGroup::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-folder-6-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-folder-6-fill';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true),
                TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('parent_id')
                    ->label('Parent Group')
                    ->options(fn () => LocationGroup::query()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id'))
                    ->nullable()
                    ->searchable()
                    ->preload(),
                Select::make('group_type')
                    ->required()
                    ->options(self::groupTypes())
                    ->default(LocationGroup::TYPE_CUSTOM),
                Select::make('status')
                    ->required()
                    ->options(self::statuses())
                    ->default(LocationGroup::STATUS_ACTIVE),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                CheckboxList::make('service_types')
                    ->options(self::serviceTypes())
                    ->columns(3),
                KeyValue::make('metadata')
                    ->columnSpanFull()
                    ->keyLabel('Key')
                    ->valueLabel('Value'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('parent.name')->label('Parent')->toggleable(),
                TextColumn::make('group_type')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('options_count')->counts('options')->label('Items')->sortable(),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options(self::statuses()),
                SelectFilter::make('group_type')->options(self::groupTypes()),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->modalDescription('Deleting a group keeps its location items and moves them to no group.'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalDescription('Deleting groups keeps their location items and moves them to no group.'),
                ]),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query->orderBy('sort_order')->orderBy('name');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLocationGroups::route('/'),
            'create' => CreateLocationGroup::route('/create'),
            'edit' => EditLocationGroup::route('/{record}/edit'),
        ];
    }

    public static function groupTypes(): array
    {
        return [
            LocationGroup::TYPE_GEO => 'Geo',
            LocationGroup::TYPE_REGION => 'Region',
            LocationGroup::TYPE_COUNTRY_BUNDLE => 'Country Bundle',
            LocationGroup::TYPE_ISP_BUNDLE => 'ISP Bundle',
            LocationGroup::TYPE_CUSTOM => 'Custom',
        ];
    }

    public static function statuses(): array
    {
        return [
            LocationGroup::STATUS_ACTIVE => 'Active',
            LocationGroup::STATUS_HIDDEN => 'Hidden',
        ];
    }

    public static function serviceTypes(): array
    {
        return [
            'vps' => 'VPS',
            'proxy' => 'Proxy',
            'vpn' => 'VPN',
        ];
    }
}
