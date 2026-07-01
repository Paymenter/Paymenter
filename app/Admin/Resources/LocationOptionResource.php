<?php

namespace App\Admin\Resources;

use App\Admin\Resources\LocationOptionResource\Pages\CreateLocationOption;
use App\Admin\Resources\LocationOptionResource\Pages\EditLocationOption;
use App\Admin\Resources\LocationOptionResource\Pages\ListLocationOptions;
use App\Models\LocationGroup;
use App\Models\LocationOption;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class LocationOptionResource extends Resource
{
    protected static ?string $model = LocationOption::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Configuration';

    protected static string|\BackedEnum|null $navigationIcon = 'ri-map-pin-line';

    protected static string|\BackedEnum|null $activeNavigationIcon = 'ri-map-pin-fill';

    protected static ?string $recordTitleAttribute = 'display_name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('display_name')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('code') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('code', Str::slug($state));
                    }),
                TextInput::make('code')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('primary_group_id')
                    ->label('Primary Group')
                    ->relationship('primaryGroup', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('legacy_id')->numeric(),
                Select::make('option_type')
                    ->required()
                    ->options(self::optionTypes())
                    ->default(LocationOption::TYPE_GEO)
                    ->live(),
                TextInput::make('place_country_iso2')
                    ->label('Country ISO2')
                    ->maxLength(2),
                TextInput::make('place_subdivision_code')
                    ->label('Subdivision Code')
                    ->maxLength(255),
                Select::make('network_type')
                    ->options(self::networkTypes())
                    ->visible(fn (Get $get): bool => in_array($get('option_type'), [LocationOption::TYPE_NETWORK_POOL, LocationOption::TYPE_ISP_POOL])),
                TextInput::make('isp_name')
                    ->visible(fn (Get $get): bool => $get('option_type') === LocationOption::TYPE_ISP_POOL),
                Select::make('selection_policy')
                    ->required()
                    ->options(self::selectionPolicies())
                    ->default(LocationOption::POLICY_FIXED),
                Select::make('status')
                    ->required()
                    ->options(self::statuses())
                    ->default(LocationOption::STATUS_ACTIVE),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
                CheckboxList::make('service_types')
                    ->options(LocationGroupResource::serviceTypes())
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
                TextColumn::make('display_name')->searchable()->sortable(),
                TextColumn::make('code')->searchable()->toggleable(),
                TextColumn::make('primaryGroup.name')->label('Group')->sortable(),
                TextColumn::make('legacy_id')->label('Legacy ID')->sortable()->toggleable(),
                TextColumn::make('option_type')->badge()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                SelectFilter::make('primary_group_id')
                    ->label('Group')
                    ->relationship('primaryGroup', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')->options(self::statuses()),
                SelectFilter::make('option_type')->options(self::optionTypes()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('move_group')
                        ->label('Move to Group')
                        ->form([
                            Select::make('primary_group_id')
                                ->label('Target Group')
                                ->options(fn () => LocationGroup::query()->orderBy('sort_order')->orderBy('name')->pluck('name', 'id'))
                                ->nullable()
                                ->searchable()
                                ->preload(),
                        ])
                        ->action(fn (Collection $records, array $data) => $records->each->update([
                            'primary_group_id' => $data['primary_group_id'] ?? null,
                        ])),
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort(function (Builder $query): Builder {
                return $query->orderBy('sort_order')->orderBy('display_name');
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLocationOptions::route('/'),
            'create' => CreateLocationOption::route('/create'),
            'edit' => EditLocationOption::route('/{record}/edit'),
        ];
    }

    public static function groupedOptions(): array
    {
        return LocationOption::query()
            ->with('primaryGroup')
            ->whereIn('status', [LocationOption::STATUS_ACTIVE, LocationOption::STATUS_HIDDEN])
            ->orderBy('sort_order')
            ->orderBy('display_name')
            ->get()
            ->groupBy(fn (LocationOption $option) => $option->primaryGroup?->name ?? 'Other')
            ->map(fn ($options) => $options->pluck('display_name', 'id')->all())
            ->all();
    }

    public static function optionTypes(): array
    {
        return [
            LocationOption::TYPE_GEO => 'Geo',
            LocationOption::TYPE_REGION => 'Region',
            LocationOption::TYPE_SYNTHETIC_POOL => 'Synthetic Pool',
            LocationOption::TYPE_ISP_POOL => 'ISP Pool',
            LocationOption::TYPE_NETWORK_POOL => 'Network Pool',
            LocationOption::TYPE_PROMO_POOL => 'Promo Pool',
            LocationOption::TYPE_UNKNOWN => 'Unknown',
        ];
    }

    public static function networkTypes(): array
    {
        return [
            LocationOption::NETWORK_DATACENTER => 'Datacenter',
            LocationOption::NETWORK_RESIDENTIAL => 'Residential',
            LocationOption::NETWORK_MOBILE => 'Mobile',
            LocationOption::NETWORK_VPN => 'VPN',
            LocationOption::NETWORK_MIXED => 'Mixed',
        ];
    }

    public static function selectionPolicies(): array
    {
        return [
            LocationOption::POLICY_FIXED => 'Fixed',
            LocationOption::POLICY_RANDOM => 'Random',
            LocationOption::POLICY_PROVIDER_DECIDES => 'Provider Decides',
        ];
    }

    public static function statuses(): array
    {
        return [
            LocationOption::STATUS_ACTIVE => 'Active',
            LocationOption::STATUS_HIDDEN => 'Hidden',
            LocationOption::STATUS_DEPRECATED => 'Deprecated',
        ];
    }
}
