<?php

namespace App\Admin\Resources\ProductResource\RelationManagers;

use App\Models\ProviderLocationOffering;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LocationOfferingsRelationManager extends RelationManager
{
    protected static string $relationship = 'locationOfferings';

    protected static ?string $title = 'Sellable Locations';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('provider_location_offering_id')
                    ->label('Provider Location')
                    ->options(fn () => $this->providerLocationOptions())
                    ->searchable()
                    ->required(),
                Checkbox::make('enabled')
                    ->default(true),
                TextInput::make('price_delta')
                    ->label('Price Delta')
                    ->numeric()
                    ->mask(RawJs::make(
                        <<<'JS'
                            $money($input, '.', '', 2)
                        JS
                    )),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('providerLocationOffering.locationOption.display_name')
            ->columns([
                TextColumn::make('providerLocationOffering.locationOption.display_name')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('providerLocationOffering.locationOption.primaryGroup.name')
                    ->label('Group')
                    ->sortable(),
                TextColumn::make('providerLocationOffering.service_type')
                    ->label('Service Type')
                    ->badge()
                    ->sortable(),
                TextColumn::make('providerLocationOffering.stock_state')
                    ->label('Stock')
                    ->badge()
                    ->sortable(),
                IconColumn::make('enabled')
                    ->boolean(),
                TextColumn::make('price_delta')
                    ->label('Price Delta')
                    ->toggleable(),
                TextColumn::make('sort_order')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->hidden(fn () => $this->getOwnerRecord()->server_id === null),
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

    protected function providerLocationOptions(): array
    {
        $serverId = $this->getOwnerRecord()->server_id;

        if (!$serverId) {
            return [];
        }

        return ProviderLocationOffering::query()
            ->with('locationOption.primaryGroup')
            ->where('provider_id', $serverId)
            ->where('enabled', true)
            ->get()
            ->groupBy(fn (ProviderLocationOffering $offering) => $offering->locationOption->primaryGroup?->name ?? 'Other')
            ->map(fn ($offerings) => $offerings->mapWithKeys(fn (ProviderLocationOffering $offering) => [
                $offering->id => $offering->locationOption->display_name . ' (' . strtoupper($offering->service_type) . ')',
            ])->all())
            ->all();
    }
}
