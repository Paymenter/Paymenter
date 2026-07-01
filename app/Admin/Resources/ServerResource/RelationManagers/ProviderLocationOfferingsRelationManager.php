<?php

namespace App\Admin\Resources\ServerResource\RelationManagers;

use App\Admin\Resources\LocationGroupResource;
use App\Admin\Resources\LocationOptionResource;
use App\Models\ProviderLocationOffering;
use App\Models\ProviderLocationTarget;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ProviderLocationOfferingsRelationManager extends RelationManager
{
    protected static string $relationship = 'providerLocationOfferings';

    protected static ?string $title = 'Provider Locations';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('location_option_id')
                    ->label('Location')
                    ->options(fn () => LocationOptionResource::groupedOptions())
                    ->searchable()
                    ->required(),
                Select::make('service_type')
                    ->required()
                    ->options(LocationGroupResource::serviceTypes())
                    ->default(ProviderLocationOffering::SERVICE_VPS),
                Checkbox::make('enabled')
                    ->default(true),
                Select::make('stock_state')
                    ->required()
                    ->options([
                        ProviderLocationOffering::STOCK_UNKNOWN => 'Unknown',
                        ProviderLocationOffering::STOCK_AVAILABLE => 'Available',
                        ProviderLocationOffering::STOCK_LIMITED => 'Limited',
                        ProviderLocationOffering::STOCK_UNAVAILABLE => 'Unavailable',
                    ])
                    ->default(ProviderLocationOffering::STOCK_UNKNOWN),
                KeyValue::make('capabilities')
                    ->columnSpanFull()
                    ->keyLabel('Capability')
                    ->valueLabel('Value'),
                Repeater::make('targets')
                    ->relationship('targets')
                    ->label('Provider Targets')
                    ->columnSpanFull()
                    ->columns(2)
                    ->defaultItems(1)
                    ->schema([
                        TextInput::make('external_location_id')
                            ->label('External Location ID')
                            ->maxLength(255),
                        TextInput::make('external_location_code')
                            ->label('External Location Code')
                            ->maxLength(255),
                        TextInput::make('external_name')
                            ->label('External Name')
                            ->maxLength(255),
                        Select::make('status')
                            ->required()
                            ->options([
                                ProviderLocationTarget::STATUS_ACTIVE => 'Active',
                                ProviderLocationTarget::STATUS_DISABLED => 'Disabled',
                            ])
                            ->default(ProviderLocationTarget::STATUS_ACTIVE),
                        TextInput::make('priority')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('weight')
                            ->numeric()
                            ->default(100)
                            ->required(),
                        KeyValue::make('raw_payload')
                            ->columnSpanFull()
                            ->keyLabel('Key')
                            ->valueLabel('Value'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('locationOption.display_name')
            ->columns([
                TextColumn::make('locationOption.display_name')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('locationOption.primaryGroup.name')
                    ->label('Group')
                    ->sortable(),
                TextColumn::make('service_type')
                    ->badge()
                    ->sortable(),
                IconColumn::make('enabled')
                    ->boolean(),
                TextColumn::make('stock_state')
                    ->badge()
                    ->sortable(),
                TextColumn::make('targets.external_location_code')
                    ->label('External Codes')
                    ->listWithLineBreaks()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('service_type')->options(LocationGroupResource::serviceTypes()),
                SelectFilter::make('stock_state')->options([
                    ProviderLocationOffering::STOCK_UNKNOWN => 'Unknown',
                    ProviderLocationOffering::STOCK_AVAILABLE => 'Available',
                    ProviderLocationOffering::STOCK_LIMITED => 'Limited',
                    ProviderLocationOffering::STOCK_UNAVAILABLE => 'Unavailable',
                ]),
            ])
            ->headerActions([
                CreateAction::make(),
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
}
